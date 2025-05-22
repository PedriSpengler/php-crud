<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adiciona a coluna 'completed' à tabela 'posts' para indicar se o post foi concluído.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->boolean('completed')->default(false);
            // Coluna booleana que indica se o post está concluído (padrão: false)
        });
    }

    /**
     * Reverse the migrations.
     * Remove a coluna 'completed' da tabela 'posts', revertendo a alteração.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('completed');
        });
    }
};
