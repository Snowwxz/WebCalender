<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        // 🔹 Kalau belum login, redirect ke login
        if (!$user) {
            return redirect()->route('login');
        } 

        // 🔹 Kalau role-nya tidak termasuk dalam daftar role yang diizinkan
        if (!in_array($user->role, $roles)) {
            abort(403, 'Unauthorized.');
        }

        // 🔹 Jika semua aman, lanjutkan request
        return $next($request);
    }
}
