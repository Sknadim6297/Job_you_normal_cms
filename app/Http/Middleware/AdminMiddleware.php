<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $adminGuard = auth('admin');
        $admin = $adminGuard->user();

        if (! $admin || ! $admin->status) {
            if ($adminGuard instanceof StatefulGuard) {
                $adminGuard->logout();
            }
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
