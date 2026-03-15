<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Financeiro;

class FinanceiroController extends Controller
{
    public function index(Request $request)
    {
        $empresaId = $request->user()->empresa_id;
        $lancamentos = Financeiro::where('empresa_id', $empresaId)->get();

        return response()->json($lancamentos);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo' => 'required|in:entrada,saida',
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric',
            'data' => 'required|date',
        ]);

        $data['empresa_id'] = $request->user()->empresa_id;
        $lancamento = Financeiro::create($data);

        return response()->json($lancamento, 201);
    }

    public function show(Request $request, $id)
    {
        $empresaId = $request->user()->empresa_id;
        $lancamento = Financeiro::where('empresa_id', $empresaId)->findOrFail($id);

        return response()->json($lancamento);
    }

    public function update(Request $request, $id)
    {
        $empresaId = $request->user()->empresa_id;
        $lancamento = Financeiro::where('empresa_id', $empresaId)->findOrFail($id);

        $data = $request->validate([
            'tipo' => 'sometimes|required|in:entrada,saida',
            'descricao' => 'sometimes|required|string|max:255',
            'valor' => 'sometimes|required|numeric',
            'data' => 'sometimes|required|date',
        ]);

        $lancamento->update($data);
        return response()->json($lancamento);
    }

    public function destroy(Request $request, $id)
    {
        $empresaId = $request->user()->empresa_id;
        $lancamento = Financeiro::where('empresa_id', $empresaId)->findOrFail($id);
        $lancamento->delete();

        return response()->json(['message' => 'Lançamento deletado com sucesso']);
    }
}