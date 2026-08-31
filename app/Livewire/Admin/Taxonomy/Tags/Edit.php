<?php

namespace App\Livewire\Admin\Taxonomy\Tags;

use App\Actions\Taxonomy\UpdateTag;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class Edit extends Component
{
    public Tag $tag;

    public string $name = '';

    public string $slug = '';

    public function mount(Tag $tag): void
    {
        $this->authorize('update', $tag);

        $this->tag = $tag;
        $this->name = $tag->name;
        $this->slug = $tag->slug;
    }

    public function updatedSlug(): void
    {
        $this->slug = Str::slug($this->slug);
    }

    public function save(): void
    {
        $this->authorize('update', $this->tag);

        try {
            app(UpdateTag::class)->handle(
                $this->tag,
                name: $this->name,
                slug: $this->slug,
            );
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->setErrorBag($exception->validator->getMessageBag());

            return;
        }

        \App\Support\Ui\Flash::success( 'Tag updated.');

        $this->redirectRoute('admin.tags.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.taxonomy.tags.edit')->layout('components.layouts.admin', [
            'title' => 'Edit tag',
        ]);
    }
}
