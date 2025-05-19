<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $roles): Response
    {
        \Log::info('User Role:', ['role' => auth()->user()->role]);

        
        $roleArray = explode(',', $roles);
        if (!in_array(auth()->user()->role, $roleArray)) {
            abort(403, 'Akses ditolak');
        }
        return $next($request);
    }
}
