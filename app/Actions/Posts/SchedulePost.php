<?php

namespace App\Actions\Posts;

use App\Actions\Posts\Concerns\ValidatesPublishablePost;
use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use App\Services\Audit\ActivityLogger;
use App\Support\Audit\ActivityEvent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SchedulePost
{
    use ValidatesPublishablePost;

    public function __construct(
        private readonly ActivityLogger $activityLogger,
    ) {}

    public function handle(Post $post, User $actor, Carbon $publishAt): Post
    {
        $this->assertTransition($post, PostStatus::Scheduled);
        $this->assertPublishable($post);
        $this->assertFuturePublishAt($publishAt);

        return DB::transaction(function () use ($post, $actor, $publishAt): Post {
            $post->status = PostStatus::Scheduled;
            $post->publish_at = $publishAt;
            $post->save();

            $this->activityLogger->record(
                $actor,
                ActivityEvent::PostScheduled,
                $post,
                [
                    'title' => $post->title,
                    'publish_at' => $publishAt->toIso8601String(),
                ],
            );

            return $post->fresh();
        });
    }

    private function assertFuturePublishAt(Carbon $publishAt): void
    {
        if (! $publishAt->isAfter(now())) {
            throw ValidationException::withMessages([
                'publish_at' => 'Scheduled publication time must be in the future.',
            ]);
        }
    }
}
