<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RestrictUserAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (Auth::check()) {
            if (Auth::user()->role != "Admin" && Auth::user()->role != "Operations Manager") {
                if (!$request->is('assigned/projects') && !$request->is('assigned/projects/*')) {

                    return redirect('/assigned/projects');
                }
            }
        }

        return $next($request);
    }
}
