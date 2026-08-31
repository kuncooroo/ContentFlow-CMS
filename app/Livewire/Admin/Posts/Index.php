<?php

namespace App\Livewire\Admin\Posts;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Queries\Content\PostIndexQuery;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public ?int $authorFilter = null;

    public ?int $categoryFilter = null;

    public string $createdFrom = '';

    public string $createdTo = '';

    public function mount(): void
    {
        $this->authorize('viewAny', Post::class);
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

    public function updatedCategoryFilter(): void
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
        $this->reset([
            'search',
            'statusFilter',
            'authorFilter',
            'categoryFilter',
            'createdFrom',
            'createdTo',
        ]);
        $this->resetPage();
    }

    public function render(): View
    {
        $user = auth()->user();
        $query = app(PostIndexQuery::class);

        $status = $this->statusFilter !== ''
            ? PostStatus::from($this->statusFilter)
            : null;

        return view('livewire.admin.posts.index', [
            'posts' => $query->paginateForUser(
                user: $user,
                search: $this->search !== '' ? $this->search : null,
                status: $status,
                authorId: $query->canFilterByAuthor($user) ? $this->authorFilter : null,
                categoryId: $this->categoryFilter,
                createdFrom: $this->createdFrom !== '' ? $this->createdFrom : null,
                createdTo: $this->createdTo !== '' ? $this->createdTo : null,
            ),
            'canFilterByAuthor' => $query->canFilterByAuthor($user),
            'authors' => $query->canFilterByAuthor($user)
                ? User::query()
                    ->whereIn('id', Post::query()->select('author_id')->distinct())
                    ->orderBy('name')
                    ->get(['id', 'name'])
                : collect(),
            'categories' => Category::query()->orderBy('name')->get(['id', 'name']),
            'statuses' => PostStatus::cases(),
            'hasActiveFilters' => $this->hasActiveFilters(),
        ])->layout('components.layouts.admin', [
            'title' => 'Posts',
        ]);
    }

    private function hasActiveFilters(): bool
    {
        return $this->search !== ''
            || $this->statusFilter !== ''
            || $this->authorFilter !== null
            || $this->categoryFilter !== null
            || $this->createdFrom !== ''
            || $this->createdTo !== '';
    }
}
