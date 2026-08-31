<?php

namespace App\Models;

use App\Enums\MenuItemType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItem extends Model
{
    /** @use HasFactory<\Database\Factories\MenuItemFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'menu_id',
        'type',
        'label',
        'page_id',
        'post_id',
        'category_id',
        'custom_url',
        'position',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => MenuItemType::class,
            'position' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Menu, $this>
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * @return BelongsTo<Page, $this>
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * @return BelongsTo<Post, $this>
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function resolvedUrl(): ?string
    {
        return match ($this->type) {
            MenuItemType::Page => $this->page ? route('public.pages.show', $this->page) : null,
            MenuItemType::Post => $this->post ? route('public.posts.show', $this->post) : null,
            MenuItemType::Category => $this->category ? route('public.categories.show', $this->category) : null,
            MenuItemType::Custom => $this->custom_url,
        };
    }

    public function isPubliclyVisibleTarget(): bool
    {
        return match ($this->type) {
            MenuItemType::Page => $this->page?->isPubliclyVisible() ?? false,
            MenuItemType::Post => $this->post?->isPubliclyVisible() ?? false,
            MenuItemType::Category => $this->category !== null,
            MenuItemType::Custom => $this->custom_url !== null && $this->custom_url !== '',
        };
    }
}
