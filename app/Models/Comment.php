<?php

namespace App\Models;

use App\Enums\CommentStatus;
use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    /** @use HasFactory<CommentFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'post_id',
        'moderated_by_user_id',
        'author_name',
        'author_email',
        'content',
        'status',
        'moderated_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => CommentStatus::class,
            'moderated_at' => 'datetime',
        ];
    }

    /**
     * @param  Builder<Comment>  $query
     * @return Builder<Comment>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', CommentStatus::Pending);
    }

    /**
     * @param  Builder<Comment>  $query
     * @return Builder<Comment>
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', CommentStatus::Approved);
    }

    /**
     * @param  Builder<Comment>  $query
     * @return Builder<Comment>
     */
    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query->where('status', CommentStatus::Approved);
    }

    /**
     * @return BelongsTo<Post, $this>
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by_user_id');
    }
}
