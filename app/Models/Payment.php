<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
      protected $fillable = [
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
}
