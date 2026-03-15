<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Authenticate
{
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if (!$request->user()) {
            return response()->json(['error' => 'Não autenticado'], 401);
        }
        return $next($request);
    }
}