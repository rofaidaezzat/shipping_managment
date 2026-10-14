<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Activity as SpatieActivity;

class Activity extends SpatieActivity
{
    protected $fillable = [
        'log_name',
        'description',
        'subject_type',
        'event',
        'subject_id',
        'causer_type',
        'causer_id',
        'created_by',
        'causer_name',
        'causer_email',
        'properties',
        'batch_uuid',
    ];

    /**
     * Relationship: The user who created/performed this transaction.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'causer_id');
    }

    /**
     * Auto-populate created_by (person's name), causer_name, causer_email on creation.
     */
    protected static function booted(): void
    {
        parent::booted();

        static::creating(function (Activity $activity) {
            $user = auth()->user();

            if (!$user && $activity->causer_id && ($activity->causer_type === User::class || empty($activity->causer_type))) {
                $user = User::find($activity->causer_id);
            }

            if ($user) {
                if (empty($activity->created_by)) {
                    $activity->created_by = $user->name; // Save the person's name directly!
                }
                if (empty($activity->causer_name)) {
                    $activity->causer_name = $user->name;
                }
                if (empty($activity->causer_email)) {
                    $activity->causer_email = $user->email;
                }
                if (empty($activity->causer_id)) {
                    $activity->causer_id = $user->id;
                    $activity->causer_type = User::class;
                }
            }
        });
    }
}
