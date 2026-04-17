<?php

namespace App\Http\Middleware; // Pastikan ini benar

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Jika belum login atau role tidak sesuai
        if (!$request->user() || !in_array($request->user()->role, $roles)) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang.');
        }

        return $next($request);
    }
}