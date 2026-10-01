<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripePaymentService
{
    public function createCheckoutSession(Order $order, Payment $payment, Course $course, User $user): Session
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        return Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($order->currency),
                    'product_data' => ['name' => $course->course_name],
                    'unit_amount' => (int) round((float) $order->amount * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'client_reference_id' => $order->order_number,
            'metadata' => [
                'payment_id' => (string) $payment->id,
                'order_id' => (string) $order->id,
                'order_number' => $order->order_number,
                'user_id' => (string) $user->id,
                'course_id' => (string) $course->id,
            ],
            'customer_email' => $user->email,
            'success_url' => route('payment.success', $order).'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payment.failed', $order),
        ], ['idempotency_key' => 'course-order-'.$order->order_number]);
    }

    public function retrieveCheckoutSession(string $sessionId): Session
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        return Session::retrieve($sessionId);
    }
}
