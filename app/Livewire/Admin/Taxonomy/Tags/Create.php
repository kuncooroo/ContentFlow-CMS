<?php

namespace App\Livewire\Admin\Taxonomy\Tags;

use App\Actions\Taxonomy\CreateTag;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class Create extends Component
{
    public string $name = '';

    public string $slug = '';

    public bool $slugManuallyEdited = false;

    public function mount(): void
    {
        $this->authorize('create', Tag::class);
    }

    public function updatedName(): void
    {
        if (! $this->slugManuallyEdited) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function updatedSlug(): void
    {
        $this->slugManuallyEdited = true;
        $this->slug = Str::slug($this->slug);
    }

    public function save(): void
    {
        $this->authorize('create', Tag::class);

        try {
            app(CreateTag::class)->handle(
                name: $this->name,
                slug: $this->slug !== '' ? $this->slug : null,
            );
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->setErrorBag($exception->validator->getMessageBag());

            return;
        }

        \App\Support\Ui\Flash::success( 'Tag created.');

        $this->redirectRoute('admin.tags.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.taxonomy.tags.create')->layout('components.layouts.admin', [
            'title' => 'Create tag',
        ]);
    }
}
