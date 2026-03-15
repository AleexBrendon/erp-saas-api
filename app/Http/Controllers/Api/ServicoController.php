<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Servico;

class ServicoController extends Controller
{
    public function index(Request $request)
    {
        $empresaId = $request->user()->empresa_id;
        $servicos = Servico::where('empresa_id', $empresaId)->get();
        return response()->json($servicos);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric',
            'duracao' => 'nullable|integer',
        ]);

        $data['empresa_id'] = $request->user()->empresa_id;
        $servico = Servico::create($data);

        return response()->json($servico, 201);
    }

    public function show(Request $request, $id)
    {
        $empresaId = $request->user()->empresa_id;
        $servico = Servico::where('empresa_id', $empresaId)->findOrFail($id);
        return response()->json($servico);
    }

    public function update(Request $request, $id)
    {
        $empresaId = $request->user()->empresa_id;
        $servico = Servico::where('empresa_id', $empresaId)->findOrFail($id);

        $data = $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'sometimes|required|numeric',
            'duracao' => 'nullable|integer',
        ]);

        $servico->update($data);
        return response()->json($servico);
    }

    public function destroy(Request $request, $id)
    {
        $empresaId = $request->user()->empresa_id;
        $servico = Servico::where('empresa_id', $empresaId)->findOrFail($id);
        $servico->delete();

        return response()->json(['message' => 'Serviço deletado com sucesso']);
    }
}