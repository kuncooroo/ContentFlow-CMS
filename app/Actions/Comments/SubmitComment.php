<?php

namespace App\Actions\Comments;

use App\Enums\CommentStatus;
use App\Models\Comment;
use App\Models\Post;
use App\Support\Comments\CommentsGate;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubmitComment
{
    public function handle(
        Post $post,
        string $authorName,
        string $authorEmail,
        string $content,
        ?string $honeypot = null,
    ): Comment {
        if ($honeypot !== null && $honeypot !== '') {
            throw ValidationException::withMessages([
                'author_name' => 'Unable to submit comment.',
            ]);
        }

        if (! CommentsGate::enabled()) {
            throw ValidationException::withMessages([
                'content' => 'Comments are currently disabled.',
            ]);
        }

        if (! $post->isPubliclyVisible()) {
            throw ValidationException::withMessages([
                'post' => 'Comments are only allowed on published posts.',
            ]);
        }

        $validated = validator([
            'author_name' => $authorName,
            'author_email' => $authorEmail,
            'content' => $content,
        ], [
            'author_name' => ['required', 'string', 'max:150'],
            'author_email' => ['required', 'string', 'email', 'max:254'],
            'content' => ['required', 'string', 'max:5000'],
        ])->validate();

        return DB::transaction(function () use ($post, $validated): Comment {
            return Comment::query()->create([
                'post_id' => $post->id,
                'author_name' => $validated['author_name'],
                'author_email' => $validated['author_email'],
                'content' => $validated['content'],
                'status' => CommentStatus::Pending,
            ]);
        });
    }
}
