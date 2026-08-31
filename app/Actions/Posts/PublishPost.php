<?php

namespace App\Actions\Posts;

use App\Actions\Posts\Concerns\ValidatesPublishablePost;
use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use App\Services\Audit\ActivityLogger;
use App\Support\Audit\ActivityEvent;
use Illuminate\Support\Facades\DB;

class PublishPost
{
    use ValidatesPublishablePost;

    public function __construct(
        private readonly ActivityLogger $activityLogger,
    ) {}

    public function handle(Post $post, ?User $actor = null): Post
    {
        if ($post->status === PostStatus::Published) {
            return $post;
        }

        $this->assertTransition($post, PostStatus::Published);
        $this->assertPublishable($post);

        return DB::transaction(function () use ($post, $actor): Post {
            $post->status = PostStatus::Published;
            $post->publish_at = now();
            $post->save();

            $this->activityLogger->record(
                $actor,
                ActivityEvent::PostPublished,
                $post,
                [
                    'title' => $post->title,
                    'slug' => $post->slug,
                ],
            );

            return $post->fresh();
        });
    }
}
