<?php

namespace App\Livewire\Admin\Taxonomy\Tags;

use App\Actions\Taxonomy\DeleteTag;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function mount(): void
    {
        $this->authorize('viewAny', Tag::class);
    }

    public function delete(int $tagId): void
    {
        $tag = Tag::query()->findOrFail($tagId);

        $this->authorize('delete', $tag);

        try {
            app(DeleteTag::class)->handle($tag);
        } catch (ValidationException $exception) {
            $this->addError('tag', $exception->validator->errors()->first('tag'));

            return;
        }

        \App\Support\Ui\Flash::success( 'Tag deleted.');
    }

    public function render(): View
    {
        return view('livewire.admin.taxonomy.tags.index', [
            'tags' => Tag::query()
                ->orderBy('name')
                ->paginate(15),
        ])->layout('components.layouts.admin', [
            'title' => 'Tags',
        ]);
    }
}
