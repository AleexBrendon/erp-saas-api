<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ItemVenda;
use App\Models\Venda;
use App\Models\Produto;
use App\Models\Servico;

class ItemVendaController extends Controller
{
    public function index(Request $request)
    {
        $empresaId = $request->user()->empresa_id;
        $itens = ItemVenda::where('empresa_id', $empresaId)
            ->with(['venda', 'produto', 'servico'])
            ->get();

        return response()->json($itens);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'venda_id' => 'required|exists:vendas,id',
            'produto_id' => 'nullable|exists:produtos,id',
            'servico_id' => 'nullable|exists:servicos,id',
            'quantidade' => 'required|integer|min:1',
            'preco_unitario' => 'required|numeric',
        ]);

        $empresaId = $request->user()->empresa_id;

        $venda = Venda::where('empresa_id', $empresaId)->findOrFail($data['venda_id']);

        if (isset($data['produto_id'])) {
            Produto::where('empresa_id', $empresaId)->findOrFail($data['produto_id']);
        }
        if (isset($data['servico_id'])) {
            Servico::where('empresa_id', $empresaId)->findOrFail($data['servico_id']);
        }

        $data['empresa_id'] = $empresaId;
        $item = ItemVenda::create($data);

        return response()->json($item, 201);
    }

    public function show(Request $request, $id)
    {
        $empresaId = $request->user()->empresa_id;
        $item = ItemVenda::where('empresa_id', $empresaId)
            ->with(['venda', 'produto', 'servico'])
            ->findOrFail($id);

        return response()->json($item);
    }

    public function update(Request $request, $id)
    {
        $empresaId = $request->user()->empresa_id;
        $item = ItemVenda::where('empresa_id', $empresaId)->findOrFail($id);

        $data = $request->validate([
            'quantidade' => 'sometimes|required|integer|min:1',
            'preco_unitario' => 'sometimes|required|numeric',
            'produto_id' => 'nullable|exists:produtos,id',
            'servico_id' => 'nullable|exists:servicos,id',
        ]);

        if (isset($data['produto_id'])) {
            Produto::where('empresa_id', $empresaId)->findOrFail($data['produto_id']);
        }
        if (isset($data['servico_id'])) {
            Servico::where('empresa_id', $empresaId)->findOrFail($data['servico_id']);
        }

        $item->update($data);
        return response()->json($item);
    }

    public function destroy(Request $request, $id)
    {
        $empresaId = $request->user()->empresa_id;
        $item = ItemVenda::where('empresa_id', $empresaId)->findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Item de venda deletado com sucesso']);
    }
}