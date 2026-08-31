<?php

namespace App\Livewire\Admin\Taxonomy\Categories;

use App\Actions\Taxonomy\DeleteCategory;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function mount(): void
    {
        $this->authorize('viewAny', Category::class);
    }

    public function delete(int $categoryId): void
    {
        $category = Category::query()->findOrFail($categoryId);

        $this->authorize('delete', $category);

        try {
            app(DeleteCategory::class)->handle($category);
        } catch (ValidationException $exception) {
            $this->addError('category', $exception->validator->errors()->first('category'));

            return;
        }

        \App\Support\Ui\Flash::success( 'Category deleted.');
    }

    public function render(): View
    {
        return view('livewire.admin.taxonomy.categories.index', [
            'categories' => Category::query()
                ->orderBy('name')
                ->paginate(15),
        ])->layout('components.layouts.admin', [
            'title' => 'Categories',
        ]);
    }
}
