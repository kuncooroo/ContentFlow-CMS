<?php

namespace App\Livewire\Admin\Media;

use App\Actions\Media\DeleteReferencedMedia;
use App\Actions\Media\StoreUploadedMedia;
use App\Models\Media;
use App\Queries\Media\MediaLibraryQuery;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $search = '';

    public ?TemporaryUploadedFile $upload = null;

    public function mount(): void
    {
        $this->authorize('viewAny', Media::class);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('search');
        $this->resetPage();
    }

    public function storeUpload(): void
    {
        $this->authorize('upload', Media::class);

        $this->validate([
            'upload' => ['required', 'file'],
        ]);

        try {
            app(StoreUploadedMedia::class)->handle($this->upload, auth()->user());
        } catch (ValidationException $exception) {
            $this->setErrorBag($exception->validator->getMessageBag());

            return;
        }

        $this->reset('upload');
        \App\Support\Ui\Flash::success( 'Media uploaded.');
    }

    public function delete(int $mediaId): void
    {
        $media = Media::query()->findOrFail($mediaId);

        $this->authorize('delete', $media);

        try {
            app(DeleteReferencedMedia::class)->handle($media, auth()->user());
        } catch (ValidationException $exception) {
            $this->addError('media', $exception->validator->errors()->first('media'));

            return;
        }

        \App\Support\Ui\Flash::success( 'Media deleted.');
    }

    public function render(): View
    {
        return view('livewire.admin.media.index', [
            'mediaItems' => app(MediaLibraryQuery::class)->paginate(
                search: $this->search !== '' ? $this->search : null,
            ),
            'hasActiveFilters' => $this->search !== '',
        ])->layout('components.layouts.admin', [
            'title' => 'Media Library',
        ]);
    }
}
