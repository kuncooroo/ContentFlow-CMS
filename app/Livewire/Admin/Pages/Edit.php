<?php

namespace App\Livewire\Admin\Pages;

use App\Actions\Pages\ArchivePage;
use App\Actions\Pages\PublishPage;
use App\Actions\Pages\RestorePageToDraft;
use App\Actions\Pages\UnpublishPage;
use App\Actions\Pages\UpdatePage;
use App\Models\Media;
use App\Models\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class Edit extends Component
{
    public Page $page;

    public string $title = '';

    public string $slug = '';

    public string $content = '';

    public ?int $og_media_id = null;

    public string $seo_title = '';

    public string $meta_description = '';

    public string $canonical_url = '';

    public bool $robots_index = true;

    public function mount(Page $page): void
    {
        $this->authorize('update', $page);

        $this->page = $page;
        $this->title = $page->title;
        $this->slug = $page->slug;
        $this->content = $page->content;
        $this->og_media_id = $page->og_media_id;
        $this->seo_title = $page->seo_title ?? '';
        $this->meta_description = $page->meta_description ?? '';
        $this->canonical_url = $page->canonical_url ?? '';
        $this->robots_index = $page->robots_index;
    }

    public function updatedSlug(): void
    {
        $this->slug = Str::slug($this->slug);
    }

    public function save(): void
    {
        $this->authorize('update', $this->page);

        try {
            app(UpdatePage::class)->handle(
                page: $this->page,
                title: $this->title,
                slug: $this->slug,
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

        \App\Support\Ui\Flash::success( 'Page updated.');

        $this->redirectRoute('admin.pages.index', navigate: true);
    }

    public function publish(): void
    {
        $this->authorize('publish', $this->page);

        try {
            $this->page = app(PublishPage::class)->handle($this->page, auth()->user());
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->setErrorBag($exception->validator->getMessageBag());

            return;
        }

        \App\Support\Ui\Flash::success( 'Page published.');
        $this->dispatch('$refresh');
    }

    public function archive(): void
    {
        $this->authorize('archive', $this->page);

        try {
            $this->page = app(ArchivePage::class)->handle($this->page, auth()->user());
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->setErrorBag($exception->validator->getMessageBag());

            return;
        }

        \App\Support\Ui\Flash::success( 'Page archived.');
        $this->dispatch('$refresh');
    }

    public function unpublish(): void
    {
        $this->authorize('unpublish', $this->page);

        try {
            $this->page = app(UnpublishPage::class)->handle($this->page);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->setErrorBag($exception->validator->getMessageBag());

            return;
        }

        \App\Support\Ui\Flash::success( 'Page unpublished and returned to draft.');
        $this->dispatch('$refresh');
    }

    public function restoreToDraft(): void
    {
        $this->authorize('restore', $this->page);

        try {
            $this->page = app(RestorePageToDraft::class)->handle($this->page);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->setErrorBag($exception->validator->getMessageBag());

            return;
        }

        \App\Support\Ui\Flash::success( 'Page restored to draft.');
        $this->dispatch('$refresh');
    }

    public function render(): View
    {
        return view('livewire.admin.pages.form', [
            'mediaItems' => Media::query()->latest()->limit(50)->get(),
            'submitLabel' => 'Save changes',
            'statusLabel' => $this->page->status->label(),
            'showPublishingActions' => true,
            'page' => $this->page,
        ])->layout('components.layouts.admin', [
            'title' => 'Edit page',
        ]);
    }
}
