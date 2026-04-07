<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $empresaId = $request->user()->empresa_id;

        $usuarios = Usuario::where('empresa_id', $empresaId)
            ->select('id', 'nome', 'email', 'role', 'avatar', 'ativo')
            ->get()
            ->map(function ($u) {
                $u->avatar = $u->avatar ? asset('storage/' . $u->avatar) : null;
                return $u;
            });

        return response()->json($usuarios);
    }

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

    public function store(Request $request)
    {
        $request->validate([
            'nome'     => 'required|string|max:255',
            'email'    => 'required|email|unique:usuarios,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,funcionario',
            'avatar'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['nome', 'email', 'role']);
        $data['empresa_id'] = $request->user()->empresa_id;
        $data['password'] = Hash::make($request->password);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');

            $data['avatar'] = $path;
        }

        $usuario = Usuario::create($data);

        return response()->json([
            'id'     => $usuario->id,
            'nome'   => $usuario->nome,
            'email'  => $usuario->email,
            'role'   => $usuario->role,
            'ativo'  => $usuario->ativo,
            'avatar' => $usuario->avatar ? asset('storage/' . $usuario->avatar) : null,
        ]);
    }

    public function show(Request $request, Usuario $usuario)
    {
        $this->authorizeEmpresa($usuario, $request);
        return response()->json($this->formatUsuario($usuario));
    }

    public function update(Request $request, Usuario $usuario)
    {
        if ($usuario->empresa_id != $request->user()->empresa_id) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $data = $request->validate([
            'nome'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|unique:usuarios,email,' . $usuario->id,
            'password' => 'sometimes|string|min:6',
            'role'     => 'sometimes|in:admin,funcionario',
            'avatar'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');

            $data['avatar'] = $path;
        }

        $usuario->update($data);

        return response()->json([
            'id'     => $usuario->id,
            'nome'   => $usuario->nome,
            'email'  => $usuario->email,
            'role'   => $usuario->role,
            'ativo'  => $usuario->ativo,
            'avatar' => $usuario->avatar ? asset('storage/' . $usuario->avatar) : null,
        ]);
    }

    public function destroy(Request $request, Usuario $usuario)
    {
        $this->authorizeEmpresa($usuario, $request);
        $usuario->delete();
        return response()->json(['message' => 'Usuário deletado com sucesso']);
    }

    public function toggleActive(Request $request, Usuario $usuario)
    {
        $this->authorizeEmpresa($usuario, $request);

        $usuario->ativo = !$usuario->ativo;
        $usuario->save();

        return response()->json([
            'message' => 'Usuário ' . ($usuario->ativo ? 'ativado' : 'desativado'),
            'usuario' => $this->formatUsuario($usuario),
        ]);
    }

    private function formatUsuario(Usuario $usuario)
    {
        return [
            'id'     => $usuario->id,
            'nome'   => $usuario->nome,
            'email'  => $usuario->email,
            'role'   => $usuario->role,
            'ativo'  => $usuario->ativo,
            'avatar' => $usuario->avatar ? asset('storage/' . $usuario->avatar) : null,
        ];
    }

    private function authorizeEmpresa(Usuario $usuario, Request $request)
    {
        if ($usuario->empresa_id != $request->user()->empresa_id) {
            abort(403, 'Não autorizado');
        }
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:usuarios,email',
        ]);

        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        $link = config('app.frontend_url') . "/reset-password?token=$token&email=" . urlencode($request->email);

        Mail::raw("Clique para redefinir sua senha: $link", function ($message) use ($request) {
            $message->to($request->email)
                ->subject('Recuperação de senha');
        });

        return response()->json([
            'message' => 'Se o e-mail existir, você receberá instruções.'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:usuarios,email',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return response()->json(['error' => 'Token inválido'], 400);
        }

        // 🔐 expiração (60 min)
        if (now()->diffInMinutes($record->created_at) > 60) {
            return response()->json(['error' => 'Token expirado'], 400);
        }

        if (!Hash::check($request->token, $record->token)) {
            return response()->json(['error' => 'Token inválido'], 400);
        }

        $usuario = Usuario::where('email', $request->email)->first();

        $usuario->password = Hash::make($request->password);
        $usuario->save();

        // 🔥 invalida sessões
        DB::table('personal_access_tokens')
            ->where('tokenable_id', $usuario->id)
            ->delete();

        // remove token
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return response()->json([
            'message' => 'Senha redefinida com sucesso'
        ]);
    }
}
