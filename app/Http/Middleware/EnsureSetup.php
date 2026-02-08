<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSetup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $adminExists = User::where('role', 'Admin')->exists();

        // Block setup if already configured
        if ($adminExists && $request->is('setup*')) {
            return redirect('/');
        }

        // Force setup if not configured
        if (! $adminExists && ! $request->is('setup*')) {
            return redirect('/setup');
        }

        return $next($request);
    }
}
