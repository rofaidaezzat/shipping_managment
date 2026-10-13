<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name',
        'code',
        'is_active',
    ];

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
