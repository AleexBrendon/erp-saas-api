<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckEmpresa;
use App\Http\Controllers\Api\EmpresaController;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\ProdutoController;
use App\Http\Controllers\Api\ServicoController;
use App\Http\Controllers\Api\VendaController;
use App\Http\Controllers\Api\ItemVendaController;
use App\Http\Controllers\Api\AgendamentoController;
use App\Http\Controllers\Api\FinanceiroController;

Route::middleware(['auth:sanctum', CheckEmpresa::class])->group(function () {

    Route::get('empresa/me', [EmpresaController::class, 'me']);

    Route::post('register', [EmpresaController::class, 'register']);
    Route::post('login', [UsuarioController::class, 'login']);

    // Logout
    Route::post('logout', [UsuarioController::class, 'logout']);

    // Rotas de usuários protegidas por admin
    Route::middleware(['admin'])->group(function () {
        // API Resource para CRUD completo
        Route::apiResource('usuarios', UsuarioController::class);

        // Toggle ativo/desativo
        Route::patch('usuarios/{usuario}/toggle', [UsuarioController::class, 'toggleActive']);
    });

    // Módulos ERP
    Route::apiResource('clientes', ClienteController::class);
    Route::apiResource('produtos', ProdutoController::class);
    Route::apiResource('servicos', ServicoController::class);
    Route::apiResource('vendas', VendaController::class);
    Route::apiResource('itens-venda', ItemVendaController::class);
    Route::apiResource('agendamentos', AgendamentoController::class);
    Route::apiResource('financeiro', FinanceiroController::class);
});
