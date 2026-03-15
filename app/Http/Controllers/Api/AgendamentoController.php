<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Servico;

class AgendamentoController extends Controller
{
    /**
     * Listar todos os agendamentos da empresa do usuário logado
     */
    public function index(Request $request)
    {
        $empresaId = $request->user()->empresa_id;

        $agendamentos = Agendamento::where('empresa_id', $empresaId)
            ->with(['cliente', 'servico', 'usuario'])
            ->get();

        return response()->json($agendamentos);
    }

    /**
     * Criar um novo agendamento
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'servico_id' => 'required|exists:servicos,id',
            'data_hora' => 'required|date_format:Y-m-d H:i:s',
            'status' => 'nullable|string|max:50',
            'observacao' => 'nullable|string',
        ]);

        $empresaId = $request->user()->empresa_id;

        // Garante que cliente e serviço pertencem à empresa do usuário
        Cliente::where('empresa_id', $empresaId)->findOrFail($data['cliente_id']);
        Servico::where('empresa_id', $empresaId)->findOrFail($data['servico_id']);

        // Divide data_hora em data e hora
        $data['data'] = date('Y-m-d', strtotime($data['data_hora']));
        $data['hora'] = date('H:i:s', strtotime($data['data_hora']));

        unset($data['data_hora']);

        $data['empresa_id'] = $empresaId;
        $data['usuario_id'] = $request->user()->id;

        $agendamento = Agendamento::create($data);

        return response()->json($agendamento, 201);
    }

    /**
     * Mostrar um agendamento específico
     */
    public function show(Request $request, $id)
    {
        $empresaId = $request->user()->empresa_id;

        $agendamento = Agendamento::where('empresa_id', $empresaId)
            ->with(['cliente', 'servico', 'usuario'])
            ->findOrFail($id);

        return response()->json($agendamento);
    }

    /**
     * Atualizar um agendamento
     */
    public function update(Request $request, $id)
    {
        $empresaId = $request->user()->empresa_id;

        $agendamento = Agendamento::where('empresa_id', $empresaId)->findOrFail($id);

        $data = $request->validate([
            'cliente_id' => 'sometimes|required|exists:clientes,id',
            'servico_id' => 'sometimes|required|exists:servicos,id',
            'data_hora' => 'sometimes|required|date_format:Y-m-d H:i:s',
            'status' => 'nullable|string|max:50',
            'observacao' => 'nullable|string',
        ]);

        if(isset($data['cliente_id'])){
            Cliente::where('empresa_id', $empresaId)->findOrFail($data['cliente_id']);
        }

        if(isset($data['servico_id'])){
            Servico::where('empresa_id', $empresaId)->findOrFail($data['servico_id']);
        }

        if(isset($data['data_hora'])){
            $data['data'] = date('Y-m-d', strtotime($data['data_hora']));
            $data['hora'] = date('H:i:s', strtotime($data['data_hora']));
            unset($data['data_hora']);
        }

        $agendamento->update($data);

        return response()->json($agendamento);
    }

    /**
     * Deletar um agendamento
     */
    public function destroy(Request $request, $id)
    {
        $empresaId = $request->user()->empresa_id;

        $agendamento = Agendamento::where('empresa_id', $empresaId)->findOrFail($id);

        $agendamento->delete();

        return response()->json(['message' => 'Agendamento deletado com sucesso']);
    }
}