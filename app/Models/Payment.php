<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
      protected $fillable = [
        'user_id',
        'course_id',
        'order_number',
        'open_order_key',
        'paid_enrollment_key',
        'order_id',
        'payment_gateway',
        'transaction_id',
        'payment_order_id',
        'payment_signature',
        'payment_response',
        'status',
        'paid_at',
        'email_queued_at',
        'stripe_session_id',
        'payment_intent',
        'customer_name',
        'customer_email',
        'amount',
        'currency',
        'payment_status',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    protected function casts(): array
    {
        return [
            'payment_response' => 'array',
            'paid_at' => 'datetime',
            'email_queued_at' => 'datetime',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function enrollment()
    {
        return $this->hasOne(Enrollment::class);
    }
}
