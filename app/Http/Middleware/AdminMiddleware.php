<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * Verifica se o usuário logado é admin.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'error' => 'Acesso negado. Apenas admins.'
            ], 403);
        }

        return $next($request);
    }
}