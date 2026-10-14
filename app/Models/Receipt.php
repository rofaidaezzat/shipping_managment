<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Receipt extends Model
{
    use LogsActivity;

    const STATUS_COMPLETED = 'completed';
    const STATUS_REJECTED  = 'rejected';

    protected $fillable = [
        'amount_paid',
        'payment_date',
        'payment_method',  // computed summary e.g. "Cash + Bank Transfer"
        'notes',
        'status',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('receipt')
            ->setDescriptionForEvent(fn(string $eventName) => "Receipt #{$this->id} (Amount: {$this->amount_paid}) was {$eventName}");
    }

    protected $casts = [
        'payment_date' => 'date',
        'amount_paid'  => 'decimal:2',
    ];

    /**
     * Check if this receipt is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if this receipt is completed/active.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED || empty($this->status);
    }

    /**
     * Scope query to non-rejected receipts.
     */
    public function scopeActive($query)
    {
        return $query->where('status', '!=', self::STATUS_REJECTED);
    }

    /**
     * Scope query to rejected receipts.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Many-to-many: a receipt can cover multiple invoices,
     * and an invoice can be covered by multiple receipts.
     * The pivot stores the amount from this receipt allocated to each invoice.
     */
    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_receipt')
                    ->withPivot('amount')
                    ->withTimestamps();
    }

    /**
     * Backward-compatible relation to linked invoice(s).
     */
    public function invoice()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_receipt')
                    ->withPivot('amount')
                    ->withTimestamps();
    }

    /**
     * A receipt has many PaymentMethod rows (one per payment method used).
     */
    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class, 'receipt_id');
    }

    /**
     * Whether this receipt was paid using multiple payment methods.
     */
    public function getIsSplitPaymentAttribute(): bool
    {
        return $this->paymentMethods()->count() > 1;
    }

    /**
     * Helper / backward-compat: first linked invoice.
     */
    public function getInvoiceAttribute()
    {
        if ($this->relationLoaded('invoice')) {
            return $this->getRelation('invoice')->first();
        }
        return $this->invoices->first();
    }

    /**
     * Helper: first linked invoice (for backward-compat display).
     */
    public function getFirstInvoiceAttribute()
    {
        return $this->invoices->first();
    }

    /**
     * Total amount due across all linked invoices.
     */
    public function getAmountDueAttribute(): float
    {
        return (float) $this->invoices->sum('total_amount');
    }
}
