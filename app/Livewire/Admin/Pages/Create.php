<?php

namespace App\Livewire\Admin\Pages;

use App\Actions\Pages\CreatePage;
use App\Models\Media;
use App\Models\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class Create extends Component
{
    public string $title = '';

    public string $slug = '';

    public string $content = '';

    public ?int $og_media_id = null;

    public string $seo_title = '';

    public string $meta_description = '';

    public string $canonical_url = '';

    public bool $robots_index = true;

    public bool $slugManuallyEdited = false;

    public function mount(): void
    {
        $this->authorize('create', Page::class);
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
        $this->authorize('create', Page::class);

        try {
            app(CreatePage::class)->handle(
                author: auth()->user(),
                title: $this->title,
                slug: $this->slug !== '' ? $this->slug : null,
                content: $this->content,
                ogMediaId: $this->og_media_id,
                seoTitle: $this->seo_title !== '' ? $this->seo_title : null,
                metaDescription: $this->meta_description !== '' ? $this->meta_description : null,
                canonicalUrl: $this->canonical_url !== '' ? $this->canonical_url : null,
                robotsIndex: $this->robots_index,
            );
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->setErrorBag($exception->validator->getMessageBag());

            return;
        }

        \App\Support\Ui\Flash::success( 'Page created as draft.');

        $this->redirectRoute('admin.pages.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.pages.form', [
            'mediaItems' => Media::query()->latest()->limit(50)->get(),
            'submitLabel' => 'Create page',
            'statusLabel' => 'Draft',
            'showPublishingActions' => false,
            'page' => null,
        ])->layout('components.layouts.admin', [
            'title' => 'Create page',
        ]);
    }
}
