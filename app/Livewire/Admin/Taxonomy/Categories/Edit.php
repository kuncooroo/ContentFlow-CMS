<?php

namespace App\Livewire\Admin\Taxonomy\Categories;

use App\Actions\Taxonomy\UpdateCategory;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class Edit extends Component
{
    public Category $category;

    public string $name = '';

    public string $slug = '';

    public string $description = '';

    public function mount(Category $category): void
    {
        $this->authorize('update', $category);

        $this->category = $category;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->description = $category->description ?? '';
    }

    public function updatedSlug(): void
    {
        $this->slug = Str::slug($this->slug);
    }

    public function save(): void
    {
        $this->authorize('update', $this->category);

        try {
            app(UpdateCategory::class)->handle(
                $this->category,
                name: $this->name,
                slug: $this->slug,
                description: $this->description !== '' ? $this->description : null,
            );
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->setErrorBag($exception->validator->getMessageBag());

            return;
        }

        \App\Support\Ui\Flash::success( 'Category updated.');

        $this->redirectRoute('admin.categories.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.taxonomy.categories.edit')->layout('components.layouts.admin', [
            'title' => 'Edit category',
        ]);
    }
}
