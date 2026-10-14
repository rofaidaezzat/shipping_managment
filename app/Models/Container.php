<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Container extends Model
{
    use LogsActivity;

    const TYPES    = ['20GP', '40GP', '40HC'];
    const STATUSES = ['AVAILABLE', 'IN_USE', 'MAINTENANCE'];

    protected $fillable = [
        'port_id',
        'container_number',
        'type',
        'status',
        'is_active',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('container')
            ->setDescriptionForEvent(fn(string $eventName) => "Container '{$this->container_number}' was {$eventName}");
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Foreign Key Relationships in Model ────────────────────────────

    /**
     * A container belongs to one port (nullable).
     * Foreign key: port_id referencing ports(id)
     */
    public function port(): BelongsTo
    {
        return $this->belongsTo(Port::class, 'port_id');
    }
}
