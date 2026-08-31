<?php

namespace App\Livewire\Admin\Posts;

use App\Actions\Posts\CreatePost;
use App\Models\Category;
use App\Models\Media;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class Create extends Component
{
    public string $title = '';

    public string $slug = '';

    public string $excerpt = '';

    public string $content = '';

    public ?int $featured_media_id = null;

    public ?int $og_media_id = null;

    public string $seo_title = '';

    public string $meta_description = '';

    public string $canonical_url = '';

    public bool $robots_index = true;

    /** @var list<int> */
    public array $selectedCategories = [];

    /** @var list<int> */
    public array $selectedTags = [];

    public bool $slugManuallyEdited = false;

    public function mount(): void
    {
        $this->authorize('create', Post::class);
    }

    public function updatedTitle(): void
    {
        if (! $this->slugManuallyEdited) {
            $this->slug = Str::slug($this->title);
        }
    }

    public function updatedSlug(): void
    {
        $this->slugManuallyEdited = true;
        $this->slug = Str::slug($this->slug);
    }

    public function save(): void
    {
        $this->authorize('create', Post::class);

        try {
            app(CreatePost::class)->handle(
                author: auth()->user(),
                title: $this->title,
                slug: $this->slug !== '' ? $this->slug : null,
                content: $this->content,
                excerpt: $this->excerpt !== '' ? $this->excerpt : null,
                featuredMediaId: $this->featured_media_id,
                categoryIds: $this->selectedCategories,
                tagIds: $this->selectedTags,
                seoTitle: $this->seo_title !== '' ? $this->seo_title : null,
                metaDescription: $this->meta_description !== '' ? $this->meta_description : null,
                canonicalUrl: $this->canonical_url !== '' ? $this->canonical_url : null,
                robotsIndex: $this->robots_index,
                ogMediaId: $this->og_media_id,
            );
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->setErrorBag($exception->validator->getMessageBag());

            return;
        }

        \App\Support\Ui\Flash::success( 'Post created as draft.');

        $this->redirectRoute('admin.posts.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.posts.form', [
            'categories' => Category::query()->orderBy('name')->get(),
            'tags' => Tag::query()->orderBy('name')->get(),
            'mediaItems' => Media::query()->latest()->limit(50)->get(),
            'submitLabel' => 'Create post',
            'statusLabel' => 'Draft',
            'showPublishingActions' => false,
            'post' => null,
        ])->layout('components.layouts.admin', [
            'title' => 'Create post',
        ]);
    }
}
