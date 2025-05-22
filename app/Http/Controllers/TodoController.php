<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TodoController extends Controller
{
    /**
     * Exibe a lista de todos os itens de tarefas (todos).
     * Busca os itens mais recentes primeiro e envia para a view via Inertia.
     */
    public function index()
    {
        $todos = Todo::latest()->get(); // Obtém todos os todos ordenados pela data mais recente
        return Inertia::render('Todos/Index', [
            'todos' => $todos // Passa os dados para o componente Vue/React na pasta Todos/Index
        ]);
    }

    /**
     * Valida e armazena um novo item de tarefa no banco de dados.
     * Após salvar, redireciona o usuário de volta para a página anterior.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',  // Título obrigatório, tipo string, máximo 255 caracteres
            'completed' => 'boolean',               // Indica se a tarefa foi concluída (opcional)
            'has_email' => 'boolean',               // Indica se a tarefa está associada a um email (opcional)
            'has_deadline' => 'boolean',            // Indica se a tarefa possui prazo (opcional)
            'deadline' => 'nullable|date',          // Data do prazo, pode ser nula
            'transferred' => 'boolean'              // Indica se a tarefa foi transferida (opcional)
        ]);

        Todo::create($validated); // Cria um novo registro no banco com os dados validados

        return redirect()->back(); // Redireciona de volta para a página anterior
    }

    /**
     * Atualiza um item de tarefa existente com os dados validados.
     * Recebe o item a ser atualizado via route model binding.
     */
    public function update(Request $request, Todo $todo)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',  // Mesma validação do método store
            'completed' => 'boolean',
            'has_email' => 'boolean',
            'has_deadline' => 'boolean',
            'deadline' => 'nullable|date',
            'transferred' => 'boolean'
        ]);

        $todo->update($validated); // Atualiza o registro no banco de dados

        return redirect()->back(); // Redireciona de volta para a página anterior
    }

    /**
     * Remove um item de tarefa do banco de dados.
     * Recebe o item a ser deletado via route model binding.
     */
    public function destroy(Todo $todo)
    {
        $todo->delete(); // Deleta o registro
        return redirect()->back(); // Redireciona de volta para a página anterior
    }
}
