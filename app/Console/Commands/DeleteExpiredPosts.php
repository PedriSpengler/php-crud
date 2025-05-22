<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Carbon\Carbon;

class DeleteExpiredPosts extends Command
{
    protected $signature = 'posts:delete-expired';
    protected $description = 'Delete posts that have reached their expiration date';

    public function handle()
    {
        $expiredPosts = Post::where('expires_at', '<=', Carbon::now())->get();

        foreach ($expiredPosts as $post) {
            $post->delete();
            $this->info("Post '{$post->title}' was deleted due to expiration.");
        }

        $this->info('Expired posts cleanup completed.');
    }
}
