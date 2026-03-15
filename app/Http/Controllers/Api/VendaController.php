<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Venda;
use App\Models\Cliente;

class VendaController extends Controller
{
    public function index(Request $request)
    {
        $empresaId = $request->user()->empresa_id;
        $vendas = Venda::where('empresa_id', $empresaId)->with('cliente')->get();
        return response()->json($vendas);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'data_venda' => 'required|date',
            'total' => 'required|numeric',
            'status' => 'nullable|string|max:50',
        ]);

        $data['empresa_id'] = $request->user()->empresa_id;

        $cliente = Cliente::where('empresa_id', $data['empresa_id'])->findOrFail($data['cliente_id']);

        $venda = Venda::create($data);

        return response()->json($venda, 201);
    }

    public function show(Request $request, $id)
    {
        $empresaId = $request->user()->empresa_id;
        $venda = Venda::where('empresa_id', $empresaId)->with('cliente')->findOrFail($id);
        return response()->json($venda);
    }

    public function update(Request $request, $id)
    {
        $empresaId = $request->user()->empresa_id;
        $venda = Venda::where('empresa_id', $empresaId)->findOrFail($id);

        $data = $request->validate([
            'cliente_id' => 'sometimes|required|exists:clientes,id',
            'data_venda' => 'sometimes|required|date',
            'total' => 'sometimes|required|numeric',
            'status' => 'nullable|string|max:50',
        ]);

        if(isset($data['cliente_id'])){
            $cliente = Cliente::where('empresa_id', $empresaId)->findOrFail($data['cliente_id']);
        }

        $venda->update($data);
        return response()->json($venda);
    }

    public function destroy(Request $request, $id)
    {
        $empresaId = $request->user()->empresa_id;
        $venda = Venda::where('empresa_id', $empresaId)->findOrFail($id);
        $venda->delete();

        return response()->json(['message' => 'Venda deletada com sucesso']);
    }
}