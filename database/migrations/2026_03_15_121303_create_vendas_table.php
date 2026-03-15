<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendas', function (Blueprint $table) {

            $table->id();

            $table->foreignId('empresa_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('cliente_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->decimal('total', 10, 2);

            $table->enum('status', ['pendente', 'pago', 'cancelado'])
                ->default('pendente');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendas');
    }
};
