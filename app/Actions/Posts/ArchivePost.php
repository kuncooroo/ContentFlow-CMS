<?php

namespace App\Actions\Posts;

use App\Actions\Posts\Concerns\ValidatesPublishablePost;
use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use App\Services\Audit\ActivityLogger;
use App\Support\Audit\ActivityEvent;
use Illuminate\Support\Facades\DB;

class ArchivePost
{
    use ValidatesPublishablePost;

    public function __construct(
        private readonly ActivityLogger $activityLogger,
    ) {}

    public function handle(Post $post, User $actor): Post
    {
        $this->assertTransition($post, PostStatus::Archived);

        return DB::transaction(function () use ($post, $actor): Post {
            $post->status = PostStatus::Archived;
            $post->save();

            $this->activityLogger->record(
                $actor,
                ActivityEvent::PostArchived,
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
