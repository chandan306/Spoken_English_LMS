<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Order;
use App\Models\Payment;
use App\Services\CoursePurchaseService;
use App\Services\StripePaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['course', 'order'])->where('user_id', auth()->id())->latest()->get();

        return view('payment', compact('payments'));
    }

    public function checkout(
        Request $request,
        Course $course,
        CoursePurchaseService $purchases,
        StripePaymentService $stripe
    ) {
        abort_unless($course->status === 'Active', 404);

        $result = $purchases->createPendingOrder($request->user(), $course);
        if ($result['type'] === 'enrolled') {
            return redirect()->route('my-courses')->with('status', 'You are already enrolled in this course.');
        }

        /** @var Order $order */
        $order = $result['order'];
        /** @var Payment $payment */
        $payment = $result['payment'];

        if ($payment->payment_order_id) {
            try {
                $session = $stripe->retrieveCheckoutSession($payment->payment_order_id);
            } catch (ApiErrorException $exception) {
                Log::warning('Could not retrieve an existing Stripe checkout session.', [
                    'order_id' => $order->id,
                    'exception' => $exception->getMessage(),
                ]);

                return redirect()->route('payment.index')->withErrors(['payment' => 'The existing checkout could not be verified. Please try again shortly.']);
            }

            if ($session->status === 'open') {
                return redirect($session->url);
            }
            if ($session->status === 'complete' && $session->payment_status !== 'paid') {
                return redirect()->route('payment.index')->with('status', 'Your payment is still processing. Your course will appear after it is confirmed.');
            }
            if ($session->payment_status === 'paid') {
                $paidOrder = $purchases->completeStripeSession($session, $order->id);
                if ($paidOrder) {
                    return view('success', ['order' => $paidOrder]);
                }
            }

            $purchases->markSessionFailed($session);

            return redirect()->route('courses.details', ['course' => $course])
                ->withErrors(['payment' => 'The previous checkout expired. You can start a new checkout.']);
        }

        try {
            $session = $stripe->createCheckoutSession($order, $payment, $course, $request->user());
            $purchases->attachCheckoutSession($payment, $session);

            return redirect($session->url);
        } catch (ApiErrorException $exception) {
            $purchases->markCheckoutFailed($payment);
            Log::error('Stripe checkout could not be created.', [
                'order_id' => $order->id,
                'exception' => $exception->getMessage(),
            ]);

            return redirect()->route('courses.details', ['course' => $course])
                ->withErrors(['payment' => 'Checkout could not be started. Please try again.']);
        }
    }

    public function success(
        Request $request,
        Order $order,
        CoursePurchaseService $purchases,
        StripePaymentService $stripe
    ) {
        $this->authorize('view', $order);
        $validated = $request->validate(['session_id' => ['required', 'string', 'max:255']]);

        try {
            $session = $stripe->retrieveCheckoutSession($validated['session_id']);
        } catch (ApiErrorException $exception) {
            Log::warning('Stripe payment return could not be verified.', [
                'order_id' => $order->id,
                'exception' => $exception->getMessage(),
            ]);

            return redirect()->route('payment.index')->withErrors(['payment' => 'The payment session could not be verified.']);
        }

        $paidOrder = $purchases->completeStripeSession($session, $order->id);
        if (!$paidOrder) {
            Log::warning('Stripe payment verification did not match the order.', ['order_id' => $order->id]);
            abort(403, 'Payment verification failed.');
        }

        return view('success', ['order' => $paidOrder]);
    }

    public function failed(Order $order)
    {
        $this->authorize('view', $order);
        $order->load(['course', 'payment']);

        return view('payment-failed', compact('order'));
    }

    public function webhook(Request $request, CoursePurchaseService $purchases)
    {
        $webhookSecret = config('services.stripe.webhook_secret');
        abort_if(!$webhookSecret, 503, 'Stripe webhook is not configured.');

        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature', ''),
                $webhookSecret
            );
        } catch (UnexpectedValueException|SignatureVerificationException $exception) {
            Log::warning('Stripe sent an invalid webhook signature.');

            return response('Invalid Stripe webhook signature.', 400);
        }

        try {
            $session = $event->data->object;
            if (in_array($event->type, ['checkout.session.completed', 'checkout.session.async_payment_succeeded'], true)) {
                $purchases->completeStripeSession($session);
            } elseif (in_array($event->type, ['checkout.session.expired', 'checkout.session.async_payment_failed'], true)) {
                $purchases->markSessionFailed($session);
            }
        } catch (\Throwable $exception) {
            Log::error('Stripe webhook processing failed.', [
                'event_id' => $event->id ?? null,
                'event_type' => $event->type,
                'exception' => $exception->getMessage(),
            ]);

            return response('Webhook processing failed.', 500);
        }

        return response()->json(['received' => true]);
    }
}
