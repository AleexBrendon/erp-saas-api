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
            'password' => 'required|string|min:6|confirmed',
            'logo'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Upload da logo
            $logoPath = $request->hasFile('logo') ? $request->file('logo')->store('logos', 'public') : null;

            $empresa = Empresa::create([
                'nome'  => $request->nome,
                'cnpj'  => $request->cnpj,
                'email' => $request->email,
                'plano' => $request->plano ?? 'free',
                'logo'  => $logoPath,
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
                'empresa' => [
                    'id'    => $empresa->id,
                    'nome'  => $empresa->nome,
                    'cnpj'  => $empresa->cnpj,
                    'email' => $empresa->email,
                    'plano' => $empresa->plano,
                    'logo'  => $empresa->logo ? asset('storage/' . $empresa->logo) : null,
                ],
                'usuario' => $usuario
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao cadastrar empresa ou usuário',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function me(Request $request)
    {
        $usuario = $request->user();

        if (!$usuario || !$usuario->empresa_id) {
            return response()->json(['message' => 'Usuário sem empresa'], 404);
        }

        $empresa = Empresa::find($usuario->empresa_id);

        if (!$empresa) {
            return response()->json(['message' => 'Empresa não encontrada'], 404);
        }

        return response()->json([
            'id'    => $empresa->id,
            'nome'  => $empresa->nome,
            'cnpj'  => $empresa->cnpj,
            'email' => $empresa->email,
            'plano' => $empresa->plano,
            'logo'  => $empresa->logo ? asset('storage/' . $empresa->logo) : null,
        ]);
    }
}