<?php

namespace App\Actions\Posts\Concerns;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Validation\ValidationException;

trait ValidatesPublishablePost
{
    protected function assertPublishable(Post $post): void
    {
        if ($post->title === '' || $post->slug === '' || $post->content === '') {
            throw ValidationException::withMessages([
                'post' => 'Post must have a title, slug, and body before publishing.',
            ]);
        }
    }

    protected function assertTransition(Post $post, PostStatus $target): void
    {
        if (! $post->status->canTransitionTo($target)) {
            throw ValidationException::withMessages([
                'status' => "Cannot transition from {$post->status->label()} to {$target->label()}.",
            ]);
        }
    }
}
