<?php

namespace App\Livewire\Admin\Media;

use App\Actions\Media\UpdateMediaMetadata;
use App\Models\Media;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Edit extends Component
{
    public Media $media;

    public string $alt_text = '';

    public function mount(Media $media): void
    {
        $this->authorize('update', $media);

        $this->media = $media;
        $this->alt_text = $media->alt_text ?? '';
    }

    public function save(): void
    {
        $this->authorize('update', $this->media);

        $validated = $this->validate([
            'alt_text' => ['nullable', 'string', 'max:500'],
        ]);

        app(UpdateMediaMetadata::class)->handle(
            $this->media,
            $validated['alt_text'] ?? null,
        );

        \App\Support\Ui\Flash::success( 'Media metadata updated.');

        $this->redirectRoute('admin.media.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.media.edit')->layout('components.layouts.admin', [
            'title' => 'Edit media',
        ]);
    }
}
