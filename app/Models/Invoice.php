<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_id',
        'date',
        'customer_name',
        'total_amount',
        'paid_amount',
        'is_paid',
        'paid_at',
    ];

    protected $casts = [
        'date'         => 'date',
        'paid_at'      => 'datetime',
        'total_amount' => 'decimal:2',
        'paid_amount'  => 'decimal:2',
        'is_paid'      => 'boolean',
    ];

    /**
     * Calculate remaining balance due on this invoice.
     */
    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float)$this->total_amount - (float)$this->paid_amount);
    }

    /**
     * An invoice can have many receipts (payments).
     */
    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    /**
     * Check whether this invoice has at least one payment receipt (protected from deletion).
     */
    public function hasReceipts(): bool
    {
        return $this->receipts()->exists();
    }
}
