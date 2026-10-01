<?php

namespace App\Services;

use App\Mail\PaymentSuccessfulMail;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class CoursePurchaseService
{
    public function __construct(private readonly InvoiceService $invoiceService)
    {
    }

    /** @return array{type: 'enrolled'|'pending'|'new', order?: Order, payment?: Payment} */
    public function createPendingOrder(User $user, Course $course): array
    {
        $openOrderKey = $user->id.':'.$course->id;

        return DB::transaction(function () use ($user, $course, $openOrderKey) {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();

            if (Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->where('status', 'active')->exists()
                || Payment::where('paid_enrollment_key', $openOrderKey)->exists()) {
                return ['type' => 'enrolled'];
            }

            $pendingPayment = Payment::with('order')
                ->where('open_order_key', $openOrderKey)
                ->where('status', 'pending')
                ->first();

            if ($pendingPayment) {
                return ['type' => 'pending', 'order' => $pendingPayment->order, 'payment' => $pendingPayment];
            }

            $orderNumber = 'ORD-'.now()->format('Ymd').'-'.strtoupper(Str::random(10));
            $order = Order::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'order_number' => $orderNumber,
                'amount' => $course->effective_price,
                'currency' => 'INR',
                'status' => 'pending',
            ]);
            $payment = Payment::create([
                'order_id' => $order->id,
                'order_number' => $orderNumber,
                'open_order_key' => $openOrderKey,
                'user_id' => $user->id,
                'course_id' => $course->id,
                'amount' => $order->amount,
                'currency' => $order->currency,
                'payment_gateway' => 'stripe',
                'payment_status' => 'pending',
                'status' => 'pending',
            ]);

            return ['type' => 'new', 'order' => $order, 'payment' => $payment];
        });
    }

    public function attachCheckoutSession(Payment $payment, object $session): void
    {
        $payment->forceFill([
            'stripe_session_id' => $session->id,
            'payment_order_id' => $session->id,
        ])->save();
    }

    public function markSessionFailed(object $session): void
    {
        $paymentId = (int) ($session->metadata->payment_id ?? 0);
        if (!$paymentId) {
            return;
        }

        DB::transaction(function () use ($session, $paymentId) {
            $payment = Payment::whereKey($paymentId)->lockForUpdate()->first();
            if (!$payment || $payment->status !== 'pending'
                || ($payment->stripe_session_id && $payment->stripe_session_id !== $session->id)) {
                return;
            }

            $payment->forceFill([
                'status' => 'failed',
                'payment_status' => 'failed',
                'open_order_key' => null,
            ])->save();
            $payment->order()->update(['status' => 'failed']);
        });
    }

    public function markCheckoutFailed(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $lockedPayment = Payment::whereKey($payment->id)->lockForUpdate()->first();
            if (!$lockedPayment || $lockedPayment->status !== 'pending') {
                return;
            }

            $lockedPayment->forceFill([
                'status' => 'failed',
                'payment_status' => 'failed',
                'open_order_key' => null,
            ])->save();
            $lockedPayment->order()->update(['status' => 'failed']);
        });
    }

    public function completeStripeSession(object $session, ?int $expectedOrderId = null): ?Order
    {
        if (($session->mode ?? null) !== 'payment' || ($session->payment_status ?? null) !== 'paid') {
            return null;
        }

        $paymentId = (int) ($session->metadata->payment_id ?? 0);
        $orderId = (int) ($session->metadata->order_id ?? 0);
        $payment = Payment::with(['order.course', 'order.user', 'order.enrollment'])
            ->whereKey($paymentId)
            ->where('order_id', $orderId)
            ->where('order_number', $session->metadata->order_number ?? null)
            ->where('user_id', (int) ($session->metadata->user_id ?? 0))
            ->where('course_id', (int) ($session->metadata->course_id ?? 0))
            ->first();

        if (!$payment || ($expectedOrderId && $payment->order_id !== $expectedOrderId)
            || ($payment->stripe_session_id && $payment->stripe_session_id !== $session->id)
            || ($session->client_reference_id ?? null) !== $payment->order_number
            || (int) ($session->amount_total ?? -1) !== (int) round((float) $payment->amount * 100)
            || strtoupper((string) ($session->currency ?? '')) !== strtoupper($payment->currency)) {
            return null;
        }

        $paidOrder = DB::transaction(function () use ($session, $payment) {
            $lockedPayment = Payment::whereKey($payment->id)->lockForUpdate()->first();
            if (!$lockedPayment) {
                return null;
            }
            $order = Order::whereKey($lockedPayment->order_id)->lockForUpdate()->first();
            if (!$order) {
                return null;
            }

            $existingEnrollment = Enrollment::where('user_id', $order->user_id)
                ->where('course_id', $order->course_id)
                ->first();
            if ($existingEnrollment && ($existingEnrollment->order_id !== $order->id
                || $existingEnrollment->payment_id !== $lockedPayment->id)) {
                return null;
            }

            $now = $lockedPayment->paid_at ?? now();
            $lockedPayment->forceFill([
                'stripe_session_id' => $session->id,
                'payment_order_id' => $session->id,
                'transaction_id' => $session->payment_intent,
                'customer_name' => $session->customer_details->name ?? $order->user->name,
                'customer_email' => $session->customer_details->email ?? $order->user->email,
                'payment_gateway' => 'stripe',
                'payment_response' => [
                    'session_id' => $session->id,
                    'payment_intent' => $session->payment_intent,
                    'payment_status' => $session->payment_status,
                    'amount_total' => $session->amount_total,
                    'currency' => $session->currency,
                ],
                'status' => 'paid',
                'payment_status' => 'paid',
                'paid_at' => $now,
                'open_order_key' => null,
                'paid_enrollment_key' => $order->user_id.':'.$order->course_id,
            ])->save();

            $order->forceFill(['status' => 'paid'])->save();

            $enrollment = Enrollment::firstOrCreate(
                ['order_id' => $order->id],
                [
                    'user_id' => $order->user_id,
                    'course_id' => $order->course_id,
                    'payment_id' => $lockedPayment->id,
                    'enrolled_at' => $now,
                    'status' => 'active',
                ]
            );
            $order->course->users()->syncWithoutDetaching([$order->user_id]);
            $this->invoiceService->createForPayment($lockedPayment, $order);

            return $order;
        });

        if (!$paidOrder) {
            return null;
        }

        $paidOrder->load(['user', 'course', 'payment', 'enrollment', 'invoice']);
        try {
            $this->invoiceService->generatePdf($paidOrder->invoice);
            $this->queuePaymentEmail($paidOrder);
        } catch (Throwable $exception) {
            Log::error('Post-payment invoice processing failed.', [
                'order_id' => $paidOrder->id,
                'exception' => $exception->getMessage(),
            ]);
        }

        return $paidOrder->fresh(['user', 'course', 'payment', 'enrollment', 'invoice']);
    }

    private function queuePaymentEmail(Order $order): void
    {
        $shouldQueue = DB::transaction(function () use ($order) {
            $payment = Payment::whereKey($order->payment->id)->lockForUpdate()->first();
            if (!$payment || $payment->email_queued_at) {
                return false;
            }

            $payment->forceFill(['email_queued_at' => now()])->save();

            return true;
        });

        if (!$shouldQueue) {
            return;
        }

        try {
            Mail::to($order->user->email)->queue(new PaymentSuccessfulMail($order->invoice));
        } catch (Throwable $exception) {
            Payment::whereKey($order->payment->id)->update(['email_queued_at' => null]);
            Log::error('Payment confirmation email could not be queued.', [
                'order_id' => $order->id,
                'exception' => $exception->getMessage(),
            ]);
        }
    }
}
