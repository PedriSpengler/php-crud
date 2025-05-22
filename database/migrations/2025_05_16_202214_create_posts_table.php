<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Cria a tabela 'posts' para armazenar publicações ou artigos.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();          // Chave primária auto-incrementável
            $table->string("title");  // Título do post, campo texto simples
            $table->text("body");     // Corpo do post, texto longo para conteúdo
            $table->timestamps();     // Campos 'created_at' e 'updated_at' para controle de datas
        });
    }

    /**
     * Reverse the migrations.
     * Remove a tabela 'posts' caso a migration seja revertida.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
