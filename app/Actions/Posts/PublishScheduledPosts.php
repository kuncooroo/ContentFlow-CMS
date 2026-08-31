<?php

namespace App\Actions\Posts;

use App\Models\Post;
use Illuminate\Support\Facades\Log;
use Throwable;

class PublishScheduledPosts
{
    public function __construct(
        private readonly PublishPost $publishPost,
    ) {}

    /**
     * @return array{
     *     published: int,
     *     failed: int,
     *     failures: list<array{post_id: int, message: string}>
     * }
     */
    public function handle(int $chunkSize = 50): array
    {
        $published = 0;
        $failed = 0;
        $failures = [];

        Post::query()
            ->dueForPublishing()
            ->orderBy('id')
            ->chunkById($chunkSize, function ($posts) use (&$published, &$failed, &$failures): void {
                foreach ($posts as $post) {
                    try {
                        $this->publishPost->handle($post, null);
                        $published++;
                    } catch (Throwable $exception) {
                        $failed++;
                        $failures[] = [
                            'post_id' => $post->id,
                            'message' => $exception->getMessage(),
                        ];

                        Log::error('Scheduled post publish failed.', [
                            'post_id' => $post->id,
                            'slug' => $post->slug,
                            'message' => $exception->getMessage(),
                        ]);
                    }
                }
            });

        return [
            'published' => $published,
            'failed' => $failed,
            'failures' => $failures,
        ];
    }
}
