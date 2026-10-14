<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Invoice extends Model
{
    use LogsActivity;

    protected $fillable = [
        'invoice_id',
        'date',
        'customer_name',
        'total_amount',
        'paid_amount',
        'is_paid',
        'paid_at',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('invoice')
            ->setDescriptionForEvent(fn(string $eventName) => "Invoice #{$this->invoice_id} ({$this->customer_name}) was {$eventName}");
    }

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
     * Many-to-many: an invoice can be covered by multiple receipts,
     * and one receipt can cover multiple invoices.
     * The pivot stores the amount allocated from that receipt to this invoice.
     */
    public function receipts()
    {
        return $this->belongsToMany(Receipt::class, 'invoice_receipt')
                    ->withPivot('amount')
                    ->withTimestamps();
    }

    /**
     * Check whether this invoice has at least one payment receipt (protected from deletion).
     */
    public function hasReceipts(): bool
    {
        return $this->receipts()->where('receipts.status', '!=', 'rejected')->exists();
    }
}
