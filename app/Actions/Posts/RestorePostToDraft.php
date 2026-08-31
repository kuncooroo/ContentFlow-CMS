<?php

namespace App\Actions\Posts;

use App\Actions\Posts\Concerns\ValidatesPublishablePost;
use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

class RestorePostToDraft
{
    use ValidatesPublishablePost;

    public function handle(Post $post): Post
    {
        $this->assertTransition($post, PostStatus::Draft);

        return DB::transaction(function () use ($post): Post {
            $post->status = PostStatus::Draft;
            $post->publish_at = null;
            $post->save();

            return $post->fresh();
        });
    }
}
