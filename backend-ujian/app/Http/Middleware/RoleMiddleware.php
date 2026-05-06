<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        // Kalau belum login
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Kalau role tidak sesuai
        if (Auth::user()->role !== $role) {
            return redirect('/login'); // atau abort(403)
        }

        return $next($request);
    }
}