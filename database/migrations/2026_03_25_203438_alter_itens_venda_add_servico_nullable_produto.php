<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('itens_venda', function (Blueprint $table) {

            $table->unsignedBigInteger('empresa_id')->nullable()->after('id');

            $table->unsignedBigInteger('produto_id')->nullable()->change();

            $table->unsignedBigInteger('servico_id')->nullable()->after('produto_id');
        });

        DB::statement("
        UPDATE itens_venda iv
        JOIN vendas v ON v.id = iv.venda_id
        SET iv.empresa_id = v.empresa_id
    ");

        Schema::table('itens_venda', function (Blueprint $table) {

            $table->unsignedBigInteger('empresa_id')->nullable(false)->change();

            $table->foreign('empresa_id')
                ->references('id')
                ->on('empresas')
                ->onDelete('cascade');

            $table->foreign('servico_id')
                ->references('id')
                ->on('servicos')
                ->onDelete('cascade');

            $table->index('empresa_id');
        });
    }

    public function down(): void
    {
        Schema::table('itens_venda', function (Blueprint $table) {

            $table->dropForeign(['empresa_id']);
            $table->dropForeign(['servico_id']);

            $table->dropColumn('empresa_id');
            $table->dropColumn('servico_id');

            $table->unsignedBigInteger('produto_id')->nullable(false)->change();
        });
    }
};
