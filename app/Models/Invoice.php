<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'user_id', 'order_id', 'payment_id', 'invoice_number', 'amount',
        'discount_amount', 'tax_amount', 'total_amount', 'invoice_date', 'pdf_path',
    ];

    protected function casts(): array
    {
        return ['invoice_date' => 'datetime'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
