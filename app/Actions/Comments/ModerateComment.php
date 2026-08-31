<?php

namespace App\Actions\Comments;

use App\Enums\CommentStatus;
use App\Models\Comment;
use App\Models\User;
use App\Services\Audit\ActivityLogger;
use App\Support\Audit\ActivityEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ModerateComment
{
    public function __construct(
        private readonly ActivityLogger $activityLogger,
    ) {}

    public function handle(Comment $comment, CommentStatus $status, User $moderator): Comment
    {
        if ($comment->status === $status) {
            return $comment;
        }

        if (! $comment->status->canTransitionTo($status)) {
            throw ValidationException::withMessages([
                'status' => "Cannot transition from {$comment->status->label()} to {$status->label()}.",
            ]);
        }

        $previousStatus = $comment->status;

        return DB::transaction(function () use ($comment, $status, $moderator, $previousStatus): Comment {
            $comment->status = $status;
            $comment->moderated_by_user_id = $moderator->id;
            $comment->moderated_at = now();
            $comment->save();

            $this->activityLogger->record(
                $moderator,
                ActivityEvent::CommentModerated,
                $comment,
                [
                    'post_id' => $comment->post_id,
                    'previous_status' => $previousStatus->value,
                    'new_status' => $status->value,
                ],
            );

            return $comment->fresh(['post', 'moderator']);
        });
    }
}
