<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if ($request->user()) {
            return response()->json(['message' => 'Você já está logado'], 403);
        }
        return $next($request);
    }
}