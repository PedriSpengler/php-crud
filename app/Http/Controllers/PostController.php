<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class PostController extends Controller
{
    /**
     * Exibe a lista de posts ativos e os posts deletados (na lixeira).
     * Faz paginação com 5 posts por página e ajusta o fuso horário do campo expires_at.
     */
    public function index(Request $request)
    {
        // Obtém posts ativos ordenados do mais recente para o mais antigo, paginados
        $posts = Post::latest()->paginate(5);
        // Ajusta o fuso horário da data de expiração para 'America/Sao_Paulo' nos posts ativos
        $posts->getCollection()->transform(function ($post) {
            if ($post->expires_at) {
                $post->expires_at = $post->expires_at->setTimezone('America/Sao_Paulo');
            }
            return $post;
        });

        // Obtém posts deletados (soft deletes), ordenados e paginados
        $trashedPosts = Post::onlyTrashed()->latest()->paginate(5);
        // Ajusta o fuso horário da data de expiração nos posts na lixeira
        $trashedPosts->getCollection()->transform(function ($post) {
            if ($post->expires_at) {
                $post->expires_at = $post->expires_at->setTimezone('America/Sao_Paulo');
            }
            return $post;
        });

        // Renderiza a view com os posts ativos e os deletados
        return Inertia::render("Posts/Index", [
            'posts' => $posts,
            'trashedPosts' => $trashedPosts
        ]);
    }

    /**
     * Exibe o formulário para criar um novo post.
     */
    public function create()
    {
        return Inertia::render("Posts/Create");
    }

    /**
     * Valida e armazena um novo post no banco de dados.
     * Converte a data de expiração do fuso horário 'America/Sao_Paulo' para UTC antes de salvar.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'expires_at' => 'nullable|date'
        ]);

        // Ajusta fuso horário da data de expiração para UTC, se fornecida
        if (isset($validated['expires_at'])) {
            $validated['expires_at'] = Carbon::parse($validated['expires_at'], 'America/Sao_Paulo')->setTimezone('UTC');
        }

        Post::create($validated); // Cria o novo post

        return back()->with('success', 'Post criado com sucesso!');
    }

    /**
     * Exibe os detalhes de um post específico.
     */
    public function show(Post $post)
    {
        return Inertia::render("Posts/Show", [
            'post' => $post
        ]);
    }

    /**
     * Exibe o formulário para editar um post existente.
     */
    public function edit(Post $post)
    {
        return Inertia::render("Posts/Edit", [
            'post' => $post
        ]);
    }

    /**
     * Valida e atualiza os dados de um post existente.
     * Ajusta a data de expiração para UTC, se fornecida.
     */
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'expires_at' => 'nullable|date'
        ]);

        if (isset($validated['expires_at'])) {
            $validated['expires_at'] = Carbon::parse($validated['expires_at'], 'America/Sao_Paulo')->setTimezone('UTC');
        }

        $post->update($validated); // Atualiza o post no banco

        return back()->with('success', 'Post atualizado com sucesso!');
    }

    /**
     * Remove (soft delete) um post, movendo-o para a lixeira.
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return back()->with('success', 'Post movido para a lixeira!');
    }

    /**
     * Restaura um post da lixeira (soft delete).
     */
    public function restore($id)
    {
        $post = Post::withTrashed()->findOrFail($id);
        $post->restore();
        return back()->with('success', 'Post restaurado com sucesso!');
    }

    /**
     * Exclui permanentemente um post da base de dados.
     */
    public function forceDelete($id)
    {
        $post = Post::withTrashed()->findOrFail($id);
        $post->forceDelete();
        return back()->with('success', 'Post excluído permanentemente!');
    }

    /**
     * Atualiza o status de conclusão (completed) de um post via requisição AJAX.
     * Retorna uma resposta JSON com o novo status.
     */
    public function updateCompleted(Request $request, Post $post)
    {
        $validated = $request->validate([
            'completed' => 'required|boolean',
        ]);

        $post->completed = $validated['completed'];
        $post->save();

        return response()->json(['success' => true, 'completed' => $post->completed]);
    }
}
