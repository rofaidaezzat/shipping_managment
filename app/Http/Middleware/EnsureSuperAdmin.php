<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    /**
     * Handle an incoming request.
     * Only a super admin can open it. Anyone else gets 403.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Must be authenticated
        if (!auth()->check()) {
            return redirect()->guest(route('login'));
        }

        $user = auth()->user();

        // 2. Local dev simulation switch (?as_user=1 to test 403, ?as_super_admin=1 to clear)
        if (app()->environment('local')) {
            if ($request->query('as_user') === '1') {
                session(['simulate_role' => 'user']);
            } elseif ($request->query('as_super_admin') === '1') {
                session()->forget('simulate_role');
            }
        }

        if (session('simulate_role') === 'user') {
            abort(403, '403 Forbidden: Access Restricted. Only a Super Admin can view System Logs. (Testing mode)');
        }

        // 3. Strictly verify Super Admin
        $isSuperAdmin = method_exists($user, 'isSuperAdmin') 
            ? $user->isSuperAdmin() 
            : (bool)($user->is_super_admin ?? false);

        if (! $isSuperAdmin) {
            abort(403, '403 Forbidden: Access Restricted. Only a Super Admin can view System Logs.');
        }

        return $next($request);
    }
}
