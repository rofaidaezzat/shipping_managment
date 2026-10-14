<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Port extends Model
{
    use LogsActivity;

    protected $fillable = [
        'company_id',
        'type',
        'name',
        'country',
        'code',
        'is_active',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('port')
            ->setDescriptionForEvent(fn(string $eventName) => "Port '{$this->name}' was {$eventName}");
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Model constraints: Prevent deleting port if it has associated containers.
     */
    protected static function booted(): void
    {
        static::deleting(function (Port $port) {
            if ($port->containers()->exists()) {
                throw new Exception("Cannot delete port '{$port->name}' because it has associated containers.");
            }
        });
    }

    // ── Foreign Key Relationships in Model ────────────────────────────

    /**
     * A port belongs to one company.
     * Foreign key: company_id referencing companies(id)
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * A port has many containers.
     * Foreign key: port_id on containers table
     */
    public function containers(): HasMany
    {
        return $this->hasMany(Container::class, 'port_id');
    }
}
