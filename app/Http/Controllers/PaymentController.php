<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\payment;
class PaymentController extends Controller
{
     /**
     * Show payment page
     */ 
    public function index()
    {
         $payments = Payment::with('course')
                ->where('user_id', auth()->id())
                ->latest()
                ->get();

        return view('student.payments', compact('payments'));
    }

    /**
     * Stripe Checkout
     */
    public function checkout(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],

            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Laravel Demo Product',
                        ],
                        'unit_amount' => 1999, // $10.00
                    ],
                    'quantity' => 1,
                ]
            ],

            'mode' => 'payment',
            'customer_email' => 'chandan.sharma@gmail.com',

            'success_url' =>
                route('payment.success') .
                '?session_id={CHECKOUT_SESSION_ID}',

            'cancel_url' => route('payment.cancel'),
        ]);

        return redirect($session->url);
    }

    /**
     * Payment Success
     */
    public function success(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::retrieve($request->session_id);
        // dd( $session);

        Payment::updateOrCreate(
            [
                'stripe_session_id' => $session->id,
            ],
            [
                'payment_intent' => $session->payment_intent,
                'customer_name' => $session->customer_details->name,
                'customer_email' => $session->customer_details->email,
                'amount' => $session->amount_total / 100,
                'currency' => strtoupper($session->currency),
                'payment_status' => $session->payment_status,
            ]
        );
         return view('success');
    }

    /**
     * Payment Cancel
     */
    public function cancel()
    {
        return "Payment Cancelled";
    }
}
