<?php

namespace App\Livewire\Admin\Posts;

use App\Actions\Posts\ArchivePost;
use App\Actions\Posts\CancelScheduledPost;
use App\Actions\Posts\PublishPost;
use App\Actions\Posts\RestorePostToDraft;
use App\Actions\Posts\SchedulePost;
use App\Actions\Posts\UnpublishPost;
use App\Actions\Posts\UpdatePost;
use App\Models\Category;
use App\Models\Media;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Component;

class Edit extends Component
{
    public Post $post;

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

    public string $scheduledAt = '';

    public function mount(Post $post): void
    {
        $this->authorize('update', $post);

        $this->post = $post->load(['categories', 'tags']);
        $this->title = $post->title;
        $this->slug = $post->slug;
        $this->excerpt = $post->excerpt ?? '';
        $this->content = $post->content;
        $this->featured_media_id = $post->featured_media_id;
        $this->og_media_id = $post->og_media_id;
        $this->seo_title = $post->seo_title ?? '';
        $this->meta_description = $post->meta_description ?? '';
        $this->canonical_url = $post->canonical_url ?? '';
        $this->robots_index = $post->robots_index;
        $this->selectedCategories = $post->categories->pluck('id')->all();
        $this->selectedTags = $post->tags->pluck('id')->all();
        $this->scheduledAt = $post->publish_at?->format('Y-m-d\TH:i') ?? '';
    }

    public function updatedSlug(): void
    {
        $this->slug = Str::slug($this->slug);
    }

    public function save(): void
    {
        $this->authorize('update', $this->post);

        try {
            app(UpdatePost::class)->handle(
                post: $this->post,
                title: $this->title,
                slug: $this->slug,
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

        \App\Support\Ui\Flash::success( 'Post updated.');

        $this->redirectRoute('admin.posts.index', navigate: true);
    }

    public function publish(): void
    {
        $this->authorize('publish', $this->post);

        try {
            $this->post = app(PublishPost::class)->handle($this->post, auth()->user());
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->setErrorBag($exception->validator->getMessageBag());

            return;
        }

        \App\Support\Ui\Flash::success( 'Post published.');
        $this->dispatch('$refresh');
    }

    public function schedule(): void
    {
        $this->authorize('schedule', $this->post);

        try {
            $this->post = app(SchedulePost::class)->handle(
                $this->post,
                auth()->user(),
                Carbon::parse($this->scheduledAt),
            );
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->setErrorBag($exception->validator->getMessageBag());

            return;
        }

        \App\Support\Ui\Flash::success( 'Post scheduled for publication.');
        $this->dispatch('$refresh');
    }

    public function cancelSchedule(): void
    {
        $this->authorize('cancelSchedule', $this->post);

        try {
            $this->post = app(CancelScheduledPost::class)->handle($this->post);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->setErrorBag($exception->validator->getMessageBag());

            return;
        }

        $this->scheduledAt = '';
        \App\Support\Ui\Flash::success( 'Schedule cancelled. Post returned to draft.');
        $this->dispatch('$refresh');
    }

    public function archive(): void
    {
        $this->authorize('archive', $this->post);

        try {
            $this->post = app(ArchivePost::class)->handle($this->post, auth()->user());
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->setErrorBag($exception->validator->getMessageBag());

            return;
        }

        \App\Support\Ui\Flash::success( 'Post archived.');
        $this->dispatch('$refresh');
    }

    public function unpublish(): void
    {
        $this->authorize('unpublish', $this->post);

        try {
            $this->post = app(UnpublishPost::class)->handle($this->post);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->setErrorBag($exception->validator->getMessageBag());

            return;
        }

        $this->scheduledAt = '';
        \App\Support\Ui\Flash::success( 'Post unpublished and returned to draft.');
        $this->dispatch('$refresh');
    }

    public function restoreToDraft(): void
    {
        $this->authorize('restore', $this->post);

        try {
            $this->post = app(RestorePostToDraft::class)->handle($this->post);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->setErrorBag($exception->validator->getMessageBag());

            return;
        }

        $this->scheduledAt = '';
        \App\Support\Ui\Flash::success( 'Post restored to draft.');
        $this->dispatch('$refresh');
    }

    public function render(): View
    {
        return view('livewire.admin.posts.form', [
            'categories' => Category::query()->orderBy('name')->get(),
            'tags' => Tag::query()->orderBy('name')->get(),
            'mediaItems' => Media::query()->latest()->limit(50)->get(),
            'submitLabel' => 'Save changes',
            'statusLabel' => $this->post->status->label(),
            'showPublishingActions' => true,
            'post' => $this->post,
        ])->layout('components.layouts.admin', [
            'title' => 'Edit post',
        ]);
    }
}
