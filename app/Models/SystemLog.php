<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Throwable;

class SystemLog extends Model
{
    protected $table = 'system_logs';

    protected $fillable = [
        'level',
        'message',
        'exception',
        'file',
        'line',
        'stack_trace',
        'user',
        'user_id',
        'created_by',   // FK → users.id — who triggered / was logged in when the error occurred
        'company',
        'url',
        'method',
        'route_name',
        'ip',
    ];

    protected $casts = [
        'line'       => 'integer',
        'user_id'    => 'integer',
        'created_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    /**
     * The authenticated user who was active when this log entry was created.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Static Logging Helpers ───────────────────────────────────────────────

    /**
     * Capture an unhandled exception or error and store in system_logs.
     */
    public static function captureException(Throwable $e, string $level = 'error'): self
    {
        $user = Auth::user();
        $userName = $user ? ($user->name ?? $user->email ?? 'User #' . $user->id) : 'Guest / System';
        $userId = $user?->id;

        // Try to identify company if present in request or route
        $company = null;
        if (Request::route('company')) {
            $compParam = Request::route('company');
            $company = is_object($compParam) ? ($compParam->name ?? $compParam->code ?? null) : (string)$compParam;
        } elseif (Request::has('company_id')) {
            $company = 'Company ID #' . Request::input('company_id');
        } elseif ($user && isset($user->company)) {
            $company = is_object($user->company) ? $user->company->name : (string)$user->company;
        }

        return self::create([
            'level'       => strtolower($level),
            'message'     => $e->getMessage() ?: get_class($e),
            'exception'   => get_class($e),
            'file'        => $e->getFile(),
            'line'        => $e->getLine(),
            'stack_trace' => $e->getTraceAsString(),
            'user'        => $userName,
            'user_id'     => $userId,
            'created_by'  => $userId,
            'company'     => $company,
            'url'         => Request::fullUrl(),
            'method'      => Request::method(),
            'route_name'  => Request::route()?->getName(),
            'ip'          => Request::ip(),
        ]);
    }

    /**
     * Manually record a custom log entry.
     */
    public static function record(string $level, string $message, array $context = []): self
    {
        $user = Auth::user();
        $userName = $context['user'] ?? ($user ? ($user->name ?? $user->email) : 'Guest / System');
        $userId = $context['user_id'] ?? $user?->id;

        return self::create([
            'level'       => strtolower($level),
            'message'     => $message,
            'exception'   => $context['exception'] ?? null,
            'file'        => $context['file'] ?? null,
            'line'        => isset($context['line']) ? (int)$context['line'] : null,
            'stack_trace' => $context['stack_trace'] ?? null,
            'user'        => $userName,
            'user_id'     => $userId,
            'created_by'  => $userId,
            'company'     => $context['company'] ?? null,
            'url'         => $context['url'] ?? Request::fullUrl(),
            'method'      => $context['method'] ?? Request::method(),
            'route_name'  => $context['route_name'] ?? Request::route()?->getName(),
            'ip'          => $context['ip'] ?? Request::ip(),
        ]);
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeLevel($query, ?string $level)
    {
        if (!empty($level)) {
            return $query->where('level', strtolower($level));
        }
        return $query;
    }

    public function scopeSearch($query, ?string $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('message', 'like', "%{$search}%")
              ->orWhere('exception', 'like', "%{$search}%")
              ->orWhere('file', 'like', "%{$search}%")
              ->orWhere('user', 'like', "%{$search}%")
              ->orWhere('company', 'like', "%{$search}%")
              ->orWhere('route_name', 'like', "%{$search}%")
              ->orWhere('ip', 'like', "%{$search}%");
        });
    }

    // ── Presenters & Formatting ──────────────────────────────────────────────

    /**
     * Get shortened file path for compact display.
     */
    public function getShortFileAttribute(): string
    {
        if (!$this->file) {
            return '—';
        }
        $parts = explode(DIRECTORY_SEPARATOR, str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $this->file));
        return implode(DIRECTORY_SEPARATOR, array_slice($parts, -3));
    }

    /**
     * Get Tailwind badge classes matching log level.
     */
    public function getBadgeClassAttribute(): string
    {
        return match (strtolower($this->level)) {
            'emergency', 'critical' => 'bg-purple-100 text-purple-800 border-purple-200',
            'error'                 => 'bg-rose-100 text-rose-800 border-rose-200',
            'warning'               => 'bg-amber-100 text-amber-800 border-amber-200',
            'notice', 'info'        => 'bg-sky-100 text-sky-800 border-sky-200',
            'debug'                 => 'bg-slate-100 text-slate-700 border-slate-200',
            default                 => 'bg-slate-100 text-slate-800 border-slate-200',
        };
    }
}
