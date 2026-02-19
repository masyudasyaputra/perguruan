<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleManager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $userRole = $request->user()->role;

        // Jika role user diizinkan, lanjut!
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // Jika tidak diizinkan, arahkan ke dashboard yang sesuai (bukan ke route asal)
        if (in_array($userRole, ['pb', 'pengprov', 'pengcab', 'admin_dojo'])) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard');
    }
}
