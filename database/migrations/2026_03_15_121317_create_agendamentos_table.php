<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agendamentos', function (Blueprint $table) {

            $table->id();

            $table->foreignId('empresa_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('cliente_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('servico_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('usuario_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('data');

            $table->time('hora');

            $table->enum('status', [
                'agendado',
                'confirmado',
                'concluido',
                'cancelado'
            ])->default('agendado');

            $table->text('observacao')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agendamentos');
    }
};