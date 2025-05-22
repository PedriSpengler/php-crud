<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Método responsável por criar as tabelas no banco de dados.
     */
    public function up(): void
    {
        // Cria a tabela 'cache' para armazenar dados de cache no banco de dados
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary(); // Chave única que identifica o item de cache (chave primária)
            $table->mediumText('value');      // Valor armazenado no cache (pode ser texto médio, JSON, etc)
            $table->integer('expiration');    // Timestamp indicando quando o cache expira (em segundos ou como timestamp unix)
        });

        // Cria a tabela 'cache_locks' para controlar bloqueios (locks) em operações de cache
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary(); // Chave que identifica o lock (chave primária)
            $table->string('owner');           // Identificador do dono do lock (ex: id do processo ou serviço)
            $table->integer('expiration');    // Timestamp indicando quando o lock expira, para evitar deadlocks
        });
    }

    /**
     * Reverse the migrations.
     * Método responsável por desfazer a criação das tabelas (drop).
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');       // Remove a tabela 'cache', caso exista
        Schema::dropIfExists('cache_locks'); // Remove a tabela 'cache_locks', caso exista
    }
};
