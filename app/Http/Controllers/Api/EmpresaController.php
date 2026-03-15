<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Usuario;
use Illuminate\Support\Facades\Validator;

class EmpresaController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome'     => 'required|string|max:255',
            'cnpj'     => 'nullable|string|unique:empresas,cnpj',
            'email'    => 'required|email|unique:empresas,email|unique:usuarios,email',
            'plano'    => 'nullable|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // 1️⃣ Criar empresa
            $empresa = Empresa::create([
                'nome'  => $request->nome,
                'cnpj'  => $request->cnpj,
                'email' => $request->email,
                'plano' => $request->plano ?? 'free',
                'password'              => 'required|string|min:6|confirmed'
            ]);

            // 2️⃣ Criar usuário admin da empresa
            $usuario = Usuario::create([
                'nome'       => $request->nome,
                'email'      => $request->email,
                'password'   => $request->password, // Laravel faz hash automático
                'role'       => 'admin',
                'empresa_id' => $empresa->id,
            ]);

            return response()->json([
                'message' => 'Empresa e admin cadastrados com sucesso',
                'empresa' => $empresa,
                'usuario' => $usuario
            ], 201);

        } catch (\Exception $e) {
            // Captura erro real do banco ou Eloquent
            return response()->json([
                'message' => 'Erro ao cadastrar empresa ou usuário',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}