<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notificacoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');

            $table->string('tipo');
            $table->string('acao');

            $table->string('titulo')->nullable();
            $table->text('mensagem');

            $table->unsignedBigInteger('referencia_id')->nullable();
            $table->string('referencia_tipo')->nullable();

            $table->boolean('lida')->default(false);

            $table->timestamps();

            $table->index(['empresa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacoes');
    }
};