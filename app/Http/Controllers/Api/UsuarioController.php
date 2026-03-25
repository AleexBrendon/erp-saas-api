<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UsuarioController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $usuario = Usuario::where('email', $request->email)->first();

        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        $token = $usuario->createToken('api_token')->plainTextToken;

        return response()->json([
            'usuario' => $usuario,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso.'
        ]);
    }

    public function index(Request $request)
    {
        $empresaId = $request->user()->empresa_id;

        return Usuario::where('empresa_id', $empresaId)
            ->select('id', 'nome', 'email', 'role')
            ->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,funcionario',
        ]);

        $data = $request->only(['nome', 'email', 'role']);
        $data['empresa_id'] = $request->user()->empresa_id;
        $data['password'] = Hash::make($request->password);

        $usuario = Usuario::create($data);

        return response()->json($usuario);
    }

    public function show(Request $request, Usuario $usuario)
    {
        if ($usuario->empresa_id != $request->empresa_id) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        return $usuario;
    }

    public function update(Request $request, Usuario $usuario)
    {
        if ($usuario->empresa_id != $request->empresa_id) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $data = $request->validate([
            'nome' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:usuarios,email,' . $usuario->id,
            'password' => 'sometimes|string|min:6',
            'role' => 'sometimes|in:admin,funcionario',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $usuario->update($data);

        return $usuario;
    }

    public function destroy(Request $request, Usuario $usuario)
    {
        if ($usuario->empresa_id != $request->empresa_id) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $usuario->delete();

        return response()->json(['message' => 'Usuário deletado']);
    }
}
