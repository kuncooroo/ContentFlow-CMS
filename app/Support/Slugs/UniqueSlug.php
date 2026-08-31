<?php

namespace App\Support\Slugs;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UniqueSlug
{
    /**
     * @param  class-string<Model>  $modelClass
     */
    public function generate(string $value, string $modelClass, string $column = 'slug', ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value);

        if ($baseSlug === '') {
            $baseSlug = 'item';
        }

        $slug = $baseSlug;
        $suffix = 2;

        while ($this->exists($modelClass, $column, $slug, $ignoreId)) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    public function generateForCategory(string $value, ?Category $ignore = null): string
    {
        return $this->generate(
            $value,
            Category::class,
            'slug',
            $ignore?->id,
        );
    }

    public function generateForTag(string $value, ?Tag $ignore = null): string
    {
        return $this->generate(
            $value,
            Tag::class,
            'slug',
            $ignore?->id,
        );
    }

    public function generateForPost(string $value, ?Post $ignore = null): string
    {
        return $this->generate(
            $value,
            Post::class,
            'slug',
            $ignore?->id,
        );
    }

    public function generateForPage(string $value, ?Page $ignore = null): string
    {
        return $this->generate(
            $value,
            Page::class,
            'slug',
            $ignore?->id,
        );
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    private function exists(string $modelClass, string $column, string $slug, ?int $ignoreId): bool
    {
        return $modelClass::query()
            ->where($column, $slug)
            ->when($ignoreId !== null, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists();
    }
}
