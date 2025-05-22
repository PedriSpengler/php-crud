<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adiciona a coluna 'expires_at' à tabela 'posts' para armazenar a data/hora de expiração.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable();
            // Coluna do tipo timestamp que pode ser nula,
            // indicando quando o post expira (pode não ter expiração).
        });
    }

    /**
     * Reverse the migrations.
     * Remove a coluna 'expires_at' da tabela 'posts', revertendo a alteração.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('expires_at');
        });
    }
};
