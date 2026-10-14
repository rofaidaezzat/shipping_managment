<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Company extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'code',
        'is_active',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('company')
            ->setDescriptionForEvent(fn(string $eventName) => "Company '{$this->name}' was {$eventName}");
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Model constraints: Prevent deleting company if it has associated ports.
     */
    protected static function booted(): void
    {
        static::deleting(function (Company $company) {
            if ($company->ports()->exists()) {
                throw new Exception("Cannot delete company '{$company->name}' because it has associated ports.");
            }
        });
    }

    // ── Foreign Key Relationships in Model ────────────────────────────

    /**
     * A company has many ports.
     * Foreign key: company_id on ports table.
     */
    public function ports(): HasMany
    {
        return $this->hasMany(Port::class, 'company_id');
    }
}
