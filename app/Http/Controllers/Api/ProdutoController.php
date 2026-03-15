<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produto;

class ProdutoController extends Controller
{
    public function index(Request $request)
    {
        $empresaId = $request->user()->empresa_id;
        $produtos = Produto::where('empresa_id', $empresaId)->get();
        return response()->json($produtos);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric',
            'estoque' => 'nullable|integer',
        ]);

        $data['empresa_id'] = $request->user()->empresa_id;
        $produto = Produto::create($data);

        return response()->json($produto, 201);
    }

    public function show(Request $request, $id)
    {
        $empresaId = $request->user()->empresa_id;
        $produto = Produto::where('empresa_id', $empresaId)->findOrFail($id);
        return response()->json($produto);
    }

    public function update(Request $request, $id)
    {
        $empresaId = $request->user()->empresa_id;
        $produto = Produto::where('empresa_id', $empresaId)->findOrFail($id);

        $data = $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'sometimes|required|numeric',
            'estoque' => 'nullable|integer',
        ]);

        $produto->update($data);
        return response()->json($produto);
    }

    public function destroy(Request $request, $id)
    {
        $empresaId = $request->user()->empresa_id;
        $produto = Produto::where('empresa_id', $empresaId)->findOrFail($id);
        $produto->delete();

        return response()->json(['message' => 'Produto deletado com sucesso']);
    }
}