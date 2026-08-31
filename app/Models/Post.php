<?php

namespace App\Models;

use App\Enums\PostStatus;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'author_id',
        'featured_media_id',
        'og_media_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'status',
        'publish_at',
        'seo_title',
        'meta_description',
        'canonical_url',
        'robots_index',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PostStatus::class,
            'publish_at' => 'datetime',
            'robots_index' => 'boolean',
        ];
    }

    /**
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PostStatus::Published);
    }

    /**
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', PostStatus::Scheduled);
    }

    /**
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('status', PostStatus::Archived);
    }

    /**
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', PostStatus::Draft);
    }

    /**
     * Posts that should appear on the public site.
     *
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query
            ->where('status', PostStatus::Published)
            ->where(function (Builder $query): void {
                $query->whereNull('publish_at')
                    ->orWhere('publish_at', '<=', now());
            });
    }

    /**
     * Scheduled posts that are due for automatic publication.
     *
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopeDueForPublishing(Builder $query): Builder
    {
        return $query
            ->where('status', PostStatus::Scheduled)
            ->whereNotNull('publish_at')
            ->where('publish_at', '<=', now());
    }

    public function isPubliclyVisible(): bool
    {
        if ($this->status !== PostStatus::Published) {
            return false;
        }

        return $this->publish_at === null || $this->publish_at->lte(now());
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * @return BelongsTo<Media, $this>
     */
    public function featuredMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_media_id');
    }

    /**
     * @return BelongsTo<Media, $this>
     */
    public function ogMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'og_media_id');
    }

    /**
     * @return BelongsToMany<Category, $this>
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'post_category');
    }

    /**
     * @return BelongsToMany<Tag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }

    /**
     * @return HasMany<Comment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return HasMany<Comment, $this>
     */
    public function approvedComments(): HasMany
    {
        return $this->hasMany(Comment::class)->publiclyVisible();
    }
}
