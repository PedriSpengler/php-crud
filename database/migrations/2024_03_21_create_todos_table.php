<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Cria a tabela 'todos' para armazenar tarefas a serem feitas.
     */
    public function up()
    {
        Schema::create('todos', function (Blueprint $table) {
            $table->id(); // Chave primária auto-incrementável
            $table->string('title'); // Título da tarefa
            $table->boolean('completed')->default(false); // Indica se a tarefa foi concluída (padrão: falso)
            $table->boolean('has_email')->default(false); // Indica se a tarefa está associada a um email (padrão: falso)
            $table->boolean('has_deadline')->default(false); // Indica se a tarefa possui um prazo definido (padrão: falso)
            $table->date('deadline')->nullable(); // Data limite para conclusão da tarefa (pode ser nula)
            $table->boolean('transferred')->default(false); // Indica se a tarefa foi transferida (padrão: falso)
            $table->timestamps(); // Colunas 'created_at' e 'updated_at' para controlar data de criação e atualização
        });
    }

    /**
     * Reverse the migrations.
     * Remove a tabela 'todos' caso a migration seja revertida.
     */
    public function down()
    {
        Schema::dropIfExists('todos');
    }
};
