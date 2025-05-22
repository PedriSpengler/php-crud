<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\DeleteExpiredPosts;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The commands that should be registered.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * These cron jobs are run in the background and do not affect the user experience.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        // Inicia o job de verificação de posts expirados
        if (!$this->isJobRunning()) {
            Log::info('Iniciando job de verificação de posts expirados');
            DeleteExpiredPosts::dispatch();
        }
    }

    /**
     * Verifica se o job já está rodando
     */
    private function isJobRunning(): bool
    {
        return Cache::has('expired_posts_job_running');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
