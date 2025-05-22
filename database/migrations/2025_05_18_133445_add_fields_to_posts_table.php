<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Cria a tabela 'posts' somente se ela ainda não existir.
     */
    public function up(): void
    {
        // Verifica se a tabela 'posts' não existe antes de criá-la
        if (!Schema::hasTable('posts')) {
            Schema::create('posts', function (Blueprint $table) {
                // Aqui você deve definir as colunas da tabela, ex:
                // $table->id();
                // $table->string('title');
                // $table->text('body');
                // $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     * Este método deveria desfazer a criação da tabela,
     * mas está vazio, o correto é dropar a tabela.
     */
    public function down(): void
    {
        // Aqui está vazio, o ideal é remover a tabela 'posts' para reverter a migration
        // Exemplo correto:
        // Schema::dropIfExists('posts');
    }
};
