<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $table = 'payment_methods';

    protected $fillable = [
        'receipt_id',
        'method',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Each PaymentMethod row belongs to one Receipt.
     */
    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }
}
