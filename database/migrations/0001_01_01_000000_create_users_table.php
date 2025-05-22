<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Método chamado para criar as tabelas no banco de dados.
     */
    public function up(): void
    {
        // Criação da tabela 'users' que armazenará os dados dos usuários do sistema
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Coluna 'id' auto-incrementável como chave primária
            $table->string('name'); // Nome do usuário
            $table->string('email')->unique(); // Email do usuário, deve ser único
            $table->timestamp('email_verified_at')->nullable(); // Data e hora em que o email foi verificado (pode ser nulo)
            $table->string('password'); // Senha do usuário (armazenada de forma segura, geralmente hash)
            $table->rememberToken(); // Token para lembrar a sessão do usuário (campo padrão do Laravel)
            $table->timestamps(); // Colunas 'created_at' e 'updated_at' para controle de criação e atualização do registro
        });

        // Criação da tabela 'password_reset_tokens' para armazenar tokens de redefinição de senha
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary(); // Email do usuário, definido como chave primária
            $table->string('token'); // Token gerado para redefinição da senha
            $table->timestamp('created_at')->nullable(); // Momento em que o token foi criado
        });

        // Criação da tabela 'sessions' para armazenar sessões ativas dos usuários
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // ID da sessão (string), chave primária
            $table->foreignId('user_id')->nullable()->index(); // Referência opcional ao usuário, indexada para performance
            $table->string('ip_address', 45)->nullable(); // Endereço IP do usuário na sessão (IPv4 ou IPv6)
            $table->text('user_agent')->nullable(); // Informações do navegador ou dispositivo do usuário
            $table->longText('payload'); // Dados da sessão armazenados em formato serializado
            $table->integer('last_activity')->index(); // Timestamp da última atividade da sessão, indexado para consultas rápidas
        });
    }

    /**
     * Reverse the migrations.
     * Método chamado para desfazer as alterações feitas no banco de dados, removendo as tabelas criadas.
     */
    public function down(): void
    {
        Schema::dropIfExists('users'); // Remove a tabela 'users' se existir
        Schema::dropIfExists('password_reset_tokens'); // Remove a tabela 'password_reset_tokens' se existir
        Schema::dropIfExists('sessions'); // Remove a tabela 'sessions' se existir
    }
};
