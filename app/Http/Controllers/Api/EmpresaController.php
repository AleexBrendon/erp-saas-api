<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Usuario;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class EmpresaController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome'     => 'required|string|max:255',
            'cnpj'     => 'nullable|string|unique:empresas,cnpj',
            'email'    => 'required|email|unique:empresas,email|unique:usuarios,email',
            'plano'    => 'nullable|string',
            'password' => 'required|string|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $empresa = Empresa::create([
                'nome'  => $request->nome,
                'cnpj'  => $request->cnpj,
                'email' => $request->email,
                'plano' => $request->plano ?? 'free'
            ]);

            $usuario = Usuario::create([
                'nome'       => $request->nome,
                'email'      => $request->email,
                'password'   => Hash::make($request->password),
                'role'       => 'admin',
                'empresa_id' => $empresa->id,
            ]);

            return response()->json([
                'message' => 'Empresa e admin cadastrados com sucesso',
                'empresa' => $empresa,
                'usuario' => $usuario
            ], 201);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Erro ao cadastrar empresa ou usuário',
                'error' => $e->getMessage()
            ], 500);

        }
    }
}