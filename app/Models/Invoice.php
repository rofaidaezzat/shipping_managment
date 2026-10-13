<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_id',
        'date',
        'customer_name',
        'paid_amount',
    ];

    protected $casts = [
        'date' => 'date',
        'paid_amount' => 'decimal:2',
    ];
}
