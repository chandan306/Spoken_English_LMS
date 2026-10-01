<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\CoursePurchaseService;
use App\Services\StripePaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Stripe\Checkout\Session;
use Tests\TestCase;

class CoursePurchaseFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Mail::fake();
    }

    public function test_course_catalog_renders_course_detail_links(): void
    {
        $course = $this->course();

        $this->get(route('courses.catalog'))
            ->assertOk()
            ->assertSee($course->course_name)
            ->assertSee(route('courses.details', ['course' => $course->slug]), false);
    }

    public function test_course_routes_fall_back_to_id_when_a_legacy_slug_is_missing(): void
    {
        $course = $this->course();
        $course->slug = null;
        $this->assertSame($course->id, $course->getRouteKey());
        $url = route('courses.details', ['course' => $course]);

        $this->assertSame('/courses/'.$course->id, parse_url($url, PHP_URL_PATH));
        $this->get($url)->assertOk()->assertSee($course->course_name);
    }

    public function test_verified_payment_creates_order_payment_enrollment_and_invoice_once(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $course = $this->course();
        $purchases = app(CoursePurchaseService::class);

        $created = $purchases->createPendingOrder($user, $course);
        $this->assertSame('new', $created['type']);
        $this->assertSame('pending', $created['order']->status);
        $this->assertSame('pending', $created['payment']->status);

        $session = $this->paidSession($created['order'], $created['payment']);
        $purchases->attachCheckoutSession($created['payment'], $session);
        $paidOrder = $purchases->completeStripeSession($session, $created['order']->id);

        $this->assertNotNull($paidOrder);
        $this->assertSame('paid', $paidOrder->status);
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseCount('enrollments', 1);
        $this->assertDatabaseCount('invoices', 1);
        $this->assertSame('paid', $paidOrder->payment->status);
        $this->assertSame('active', $paidOrder->enrollment->status);
        Storage::disk('local')->assertExists($paidOrder->invoice->pdf_path);
        Mail::assertQueued(\App\Mail\PaymentSuccessfulMail::class, 1);

        $purchases->completeStripeSession($session, $created['order']->id);
        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseCount('enrollments', 1);
        $this->assertDatabaseCount('invoices', 1);
        Mail::assertQueued(\App\Mail\PaymentSuccessfulMail::class, 1);
    }

    public function test_amount_mismatch_does_not_complete_an_order(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $course = $this->course();
        $purchases = app(CoursePurchaseService::class);
        $created = $purchases->createPendingOrder($user, $course);
        $session = $this->paidSession($created['order'], $created['payment']);
        $session->amount_total--;

        $this->assertNull($purchases->completeStripeSession($session, $created['order']->id));
        $this->assertDatabaseHas('orders', ['id' => $created['order']->id, 'status' => 'pending']);
        $this->assertDatabaseCount('enrollments', 0);
        $this->assertDatabaseCount('invoices', 0);
    }

    public function test_my_courses_and_invoice_download_are_limited_to_the_owner(): void
    {
        $owner = User::factory()->create(['role' => 'student']);
        $otherUser = User::factory()->create(['role' => 'student']);
        $course = $this->course();
        $purchases = app(CoursePurchaseService::class);
        $created = $purchases->createPendingOrder($owner, $course);
        $session = $this->paidSession($created['order'], $created['payment']);
        $purchases->attachCheckoutSession($created['payment'], $session);
        $paidOrder = $purchases->completeStripeSession($session, $created['order']->id);
        $invoice = $paidOrder->invoice;
        $this->assertTrue($owner->can('view', $invoice), 'Invoice ownership policy denied the invoice owner.');

        $ownerCoursesResponse = $this->actingAs($owner)->get(route('my-courses'));
        $this->assertSame(200, $ownerCoursesResponse->status(), $ownerCoursesResponse->getContent());
        $ownerCoursesResponse->assertSee($course->course_name);
        $otherCoursesResponse = $this->actingAs($otherUser)->get(route('my-courses'));
        $this->assertSame(200, $otherCoursesResponse->status(), $otherCoursesResponse->getContent());
        $otherCoursesResponse->assertDontSee($course->course_name);
        $this->actingAs($otherUser)->get(route('invoices.download', $invoice))
            ->assertForbidden();
        $ownedInvoiceResponse = $this->actingAs($owner)->get(route('invoices.download', $invoice));
        $this->assertSame(200, $ownedInvoiceResponse->getStatusCode(), $ownedInvoiceResponse->getContent());
        $ownedInvoiceResponse->assertHeader('content-type', 'application/pdf');
    }

    public function test_payment_success_page_uses_verified_gateway_details_and_checks_order_owner(): void
    {
        $owner = User::factory()->create(['role' => 'student']);
        $otherUser = User::factory()->create(['role' => 'student']);
        $course = $this->course();
        $purchases = app(CoursePurchaseService::class);
        $created = $purchases->createPendingOrder($owner, $course);
        $session = $this->paidSession($created['order'], $created['payment']);
        $purchases->attachCheckoutSession($created['payment'], $session);
        $this->assertTrue($owner->can('view', $created['order']), 'Order ownership policy denied the order owner.');

        $this->actingAs($otherUser)->get(route('payment.success', [
            'order' => $created['order'],
            'session_id' => $session->id,
        ]))->assertForbidden();

        $stripe = \Mockery::mock(StripePaymentService::class);
        $stripe->shouldReceive('retrieveCheckoutSession')->once()->with($session->id)->andReturn($session);
        $this->app->instance(StripePaymentService::class, $stripe);
        $successResponse = $this->actingAs($owner)->get(route('payment.success', [
            'order' => $created['order'],
            'session_id' => $session->id,
        ]));
        $this->assertSame(200, $successResponse->status(), $successResponse->getContent());
        $successResponse->assertSee($created['order']->order_number)->assertSee('Payment Successful');
    }

    public function test_invalid_stripe_webhook_signature_is_rejected(): void
    {
        config(['services.stripe.webhook_secret' => 'whsec_test']);

        $this->call('POST', route('stripe.webhook'), [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_STRIPE_SIGNATURE' => 'invalid-signature',
        ], '{"type":"checkout.session.completed","data":{"object":{}}}')
            ->assertBadRequest();
    }

    private function course(): Course
    {
        return Course::create([
            'course_name' => 'Business English',
            'slug' => 'business-english',
            'description' => 'A professional English course.',
            'price' => 2500,
            'discount_price' => 1999,
            'duration' => 30,
            'status' => 'Active',
        ]);
    }

    private function paidSession(Order $order, Payment $payment): Session
    {
        return Session::constructFrom([
            'id' => 'cs_test_'.$order->id,
            'object' => 'checkout.session',
            'mode' => 'payment',
            'status' => 'complete',
            'payment_status' => 'paid',
            'client_reference_id' => $order->order_number,
            'metadata' => [
                'payment_id' => (string) $payment->id,
                'order_id' => (string) $order->id,
                'order_number' => $order->order_number,
                'user_id' => (string) $order->user_id,
                'course_id' => (string) $order->course_id,
            ],
            'payment_intent' => 'pi_test_'.$order->id,
            'amount_total' => 199900,
            'currency' => 'inr',
            'customer_details' => [
                'name' => 'Test Customer',
                'email' => 'customer@example.com',
            ],
        ]);
    }
}
