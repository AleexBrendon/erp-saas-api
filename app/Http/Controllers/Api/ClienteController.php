<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cliente;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $empresaId = $request->user()->empresa_id;
        $clientes = Cliente::where('empresa_id', $empresaId)->get();
        return response()->json($clientes);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:50',
            'documento' => 'nullable|string|max:50',
        ]);

        $data['empresa_id'] = $request->user()->empresa_id;
        $cliente = Cliente::create($data);

        return response()->json($cliente, 201);
    }

    public function show(Request $request, $id)
    {
        $empresaId = $request->user()->empresa_id;
        $cliente = Cliente::where('empresa_id', $empresaId)->findOrFail($id);
        return response()->json($cliente);
    }

    public function update(Request $request, $id)
    {
        $empresaId = $request->user()->empresa_id;
        $cliente = Cliente::where('empresa_id', $empresaId)->findOrFail($id);

        $data = $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:50',
            'documento' => 'nullable|string|max:50',
        ]);

        $cliente->update($data);
        return response()->json($cliente);
    }

    public function destroy(Request $request, $id)
    {
        $empresaId = $request->user()->empresa_id;
        $cliente = Cliente::where('empresa_id', $empresaId)->findOrFail($id);
        $cliente->delete();

        return response()->json(['message' => 'Cliente deletado com sucesso']);
    }
}