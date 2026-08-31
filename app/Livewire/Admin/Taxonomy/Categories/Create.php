<?php

namespace App\Livewire\Admin\Taxonomy\Categories;

use App\Actions\Taxonomy\CreateCategory;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class Create extends Component
{
    public string $name = '';

    public string $slug = '';

    public string $description = '';

    public bool $slugManuallyEdited = false;

    public function mount(): void
    {
        $this->authorize('create', Category::class);
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
        $this->authorize('create', Category::class);

        try {
            app(CreateCategory::class)->handle(
                name: $this->name,
                slug: $this->slug !== '' ? $this->slug : null,
                description: $this->description !== '' ? $this->description : null,
            );
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->setErrorBag($exception->validator->getMessageBag());

            return;
        }

        \App\Support\Ui\Flash::success( 'Category created.');

        $this->redirectRoute('admin.categories.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.taxonomy.categories.create')->layout('components.layouts.admin', [
            'title' => 'Create category',
        ]);
    }
}
