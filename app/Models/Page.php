<?php

namespace App\Models;

use App\Enums\PageStatus;
use Database\Factories\PageFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Page extends Model
{
    /** @use HasFactory<PageFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'author_id',
        'og_media_id',
        'title',
        'slug',
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
            'status' => PageStatus::class,
            'publish_at' => 'datetime',
            'robots_index' => 'boolean',
        ];
    }

    /**
     * @param  Builder<Page>  $query
     * @return Builder<Page>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PageStatus::Published);
    }

    /**
     * @param  Builder<Page>  $query
     * @return Builder<Page>
     */
    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('status', PageStatus::Archived);
    }

    /**
     * @param  Builder<Page>  $query
     * @return Builder<Page>
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', PageStatus::Draft);
    }

    /**
     * Pages that should appear on the public site.
     *
     * @param  Builder<Page>  $query
     * @return Builder<Page>
     */
    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query
            ->where('status', PageStatus::Published)
            ->where(function (Builder $query): void {
                $query->whereNull('publish_at')
                    ->orWhere('publish_at', '<=', now());
            });
    }

    public function isPubliclyVisible(): bool
    {
        if ($this->status !== PageStatus::Published) {
            return false;
        }

        return $this->publish_at === null || $this->publish_at->lte(now());
    }

    public function isReferencedByMenu(): bool
    {
        if (! Schema::hasTable('menu_items')) {
            return false;
        }

        return DB::table('menu_items')
            ->where('page_id', $this->id)
            ->exists();
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
    public function ogMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'og_media_id');
    }
}
