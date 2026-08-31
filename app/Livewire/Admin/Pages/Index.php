<?php

namespace App\Livewire\Admin\Pages;

use App\Actions\Pages\DeletePage;
use App\Enums\PageStatus;
use App\Models\Page;
use App\Models\User;
use App\Queries\Content\PageIndexQuery;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public ?int $authorFilter = null;

    public string $createdFrom = '';

    public string $createdTo = '';

    public function mount(): void
    {
        $this->authorize('viewAny', Page::class);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedAuthorFilter(): void
    {
        $this->resetPage();
    }

    public function updatedCreatedFrom(): void
    {
        $this->resetPage();
    }

    public function updatedCreatedTo(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'statusFilter', 'authorFilter', 'createdFrom', 'createdTo']);
        $this->resetPage();
    }

    public function delete(int $pageId): void
    {
        $page = Page::query()->findOrFail($pageId);

        $this->authorize('delete', $page);

        try {
            app(DeletePage::class)->handle($page);
        } catch (ValidationException $exception) {
            $this->addError('page', $exception->validator->errors()->first('page'));

            return;
        }

        \App\Support\Ui\Flash::success( 'Page deleted.');
    }

    public function render(): View
    {
        $status = $this->statusFilter !== ''
            ? PageStatus::from($this->statusFilter)
            : null;

        return view('livewire.admin.pages.index', [
            'pages' => app(PageIndexQuery::class)->paginate(
                search: $this->search !== '' ? $this->search : null,
                status: $status,
                authorId: $this->authorFilter,
                createdFrom: $this->createdFrom !== '' ? $this->createdFrom : null,
                createdTo: $this->createdTo !== '' ? $this->createdTo : null,
            ),
            'authors' => User::query()
                ->whereIn('id', Page::query()->select('author_id')->distinct())
                ->orderBy('name')
                ->get(['id', 'name']),
            'statuses' => PageStatus::cases(),
            'hasActiveFilters' => $this->search !== ''
                || $this->statusFilter !== ''
                || $this->authorFilter !== null
                || $this->createdFrom !== ''
                || $this->createdTo !== '',
        ])->layout('components.layouts.admin', [
            'title' => 'Pages',
        ]);
    }
}
