<?php

namespace App\Jobs;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DeleteExpiredPosts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Job DeleteExpiredPosts iniciado');

        // Marca que o job está rodando
        Cache::put('expired_posts_job_running', true, now()->addMinutes(2));

        try {
            // Pega o momento atual em UTC
            $now = Carbon::now('UTC');
            Log::info('Verificando posts expirados', ['now' => $now->format('Y-m-d H:i:s')]);

            // Busca posts que expiraram (incluindo os que expiram exatamente agora)
            $expiredPosts = Post::where('expires_at', '<=', $now)
                ->whereNotNull('expires_at') // Garante que só pega posts com data de expiração
                ->get();

            Log::info('Posts encontrados para verificação', ['count' => $expiredPosts->count()]);

            foreach ($expiredPosts as $post) {
                // Converte a data de expiração para o timezone local para logging
                $localExpiration = Carbon::parse($post->expires_at)->setTimezone('America/Sao_Paulo');

                // Log para debug
                Log::info("Post expirado: {$post->title}", [
                    'id' => $post->id,
                    'expires_at' => $localExpiration->format('Y-m-d H:i:s'),
                    'deleted_at' => $now->format('Y-m-d H:i:s')
                ]);

                $post->delete();
            }

            // Re-dispatch o job para rodar novamente em 1 minuto
            Log::info('Agendando próxima execução do job');
            DeleteExpiredPosts::dispatch()->delay(now()->addMinute());
        } catch (\Exception $e) {
            Log::error('Erro ao processar posts expirados', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            // Remove a marca de job rodando
            Cache::forget('expired_posts_job_running');
            Log::info('Job DeleteExpiredPosts finalizado');
        }
    }
}
