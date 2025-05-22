<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adiciona a coluna 'deleted_at' para permitir soft deletes na tabela 'posts'.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->softDeletes();
            // Cria uma coluna 'deleted_at' do tipo timestamp que pode ser nula.
            // Essa coluna indica se o registro foi "excluído" logicamente (soft delete).
        });
    }

    /**
     * Reverse the migrations.
     * Remove a coluna 'deleted_at', desabilitando o soft delete na tabela 'posts'.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropSoftDeletes();
            // Remove a coluna 'deleted_at' da tabela.
        });
    }
};
