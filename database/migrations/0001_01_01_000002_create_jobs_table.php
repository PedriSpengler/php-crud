<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Cria as tabelas relacionadas ao sistema de filas (jobs) do Laravel.
     */
    public function up(): void
    {
        // Tabela que armazena os jobs pendentes ou em processamento
        Schema::create('jobs', function (Blueprint $table) {
            $table->id(); // ID auto-incrementável, chave primária
            $table->string('queue')->index(); // Nome da fila a qual o job pertence, indexado para busca rápida
            $table->longText('payload'); // Dados serializados do job a ser executado (classe, método, dados)
            $table->unsignedTinyInteger('attempts'); // Número de tentativas já realizadas para este job
            $table->unsignedInteger('reserved_at')->nullable(); // Timestamp que indica quando o job foi reservado para execução
            $table->unsignedInteger('available_at'); // Timestamp indicando quando o job está disponível para ser executado
            $table->unsignedInteger('created_at'); // Timestamp da criação do job
        });

        // Tabela que armazena lotes (batches) de jobs para execução em grupo
        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary(); // ID único do lote (string)
            $table->string('name'); // Nome do lote
            $table->integer('total_jobs'); // Total de jobs no lote
            $table->integer('pending_jobs'); // Número de jobs ainda pendentes no lote
            $table->integer('failed_jobs'); // Número de jobs que falharam no lote
            $table->longText('failed_job_ids'); // IDs dos jobs que falharam (armazenados em formato serializado ou JSON)
            $table->mediumText('options')->nullable(); // Opções adicionais do lote (JSON ou array serializado)
            $table->integer('cancelled_at')->nullable(); // Timestamp quando o lote foi cancelado, se aplicável
            $table->integer('created_at'); // Timestamp da criação do lote
            $table->integer('finished_at')->nullable(); // Timestamp de finalização do lote, se já concluído
        });

        // Tabela que armazena os jobs que falharam na execução
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id(); // ID auto-incrementável, chave primária
            $table->string('uuid')->unique(); // Identificador único universal do job que falhou
            $table->text('connection'); // Nome da conexão da fila usada
            $table->text('queue'); // Nome da fila onde o job foi enviado
            $table->longText('payload'); // Dados do job que falhou (serializados)
            $table->longText('exception'); // Detalhes da exceção ou erro ocorrido durante a execução
            $table->timestamp('failed_at')->useCurrent(); // Data e hora em que o job falhou (padrão: momento da inserção)
        });
    }

    /**
     * Reverse the migrations.
     * Remove as tabelas criadas caso seja necessário reverter a migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs'); // Remove tabela 'jobs'
        Schema::dropIfExists('job_batches'); // Remove tabela 'job_batches'
        Schema::dropIfExists('failed_jobs'); // Remove tabela 'failed_jobs'
    }
};
