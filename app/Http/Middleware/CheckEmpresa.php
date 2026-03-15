<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Cliente;

class CheckEmpresa
{
    public function handle(Request $request, Closure $next)
    {
        $clienteId = $request->route('id');
        $cliente = Cliente::find($clienteId);

        if ($cliente && $cliente->empresa_id != $request->user()->empresa_id) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        return $next($request);
    }
}
