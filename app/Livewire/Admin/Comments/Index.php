<?php

namespace App\Livewire\Admin\Comments;

use App\Actions\Comments\ModerateComment;
use App\Enums\CommentStatus;
use App\Models\Comment;
use App\Queries\Comments\CommentModerationQuery;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $statusFilter = 'pending';

    public string $search = '';

    public function mount(): void
    {
        $this->authorize('viewAny', Comment::class);
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'statusFilter']);
        $this->statusFilter = 'pending';
        $this->resetPage();
    }

    public function moderate(int $commentId, string $status): void
    {
        $comment = Comment::query()->findOrFail($commentId);

        $this->authorize('moderate', $comment);

        try {
            app(ModerateComment::class)->handle(
                $comment,
                CommentStatus::from($status),
                auth()->user(),
            );
        } catch (ValidationException $exception) {
            $this->addError('moderation', $exception->validator->errors()->first('status'));

            return;
        }

        \App\Support\Ui\Flash::success( 'Comment moderation updated.');
    }

    public function render(): View
    {
        $moderationQuery = app(CommentModerationQuery::class);

        $status = $this->statusFilter === 'all'
            ? null
            : CommentStatus::from($this->statusFilter);

        return view('livewire.admin.comments.index', [
            'comments' => $moderationQuery->paginate(
                status: $status,
                search: $this->search !== '' ? $this->search : null,
            ),
            'pendingCount' => $moderationQuery->pendingCount(),
            'hasActiveFilters' => $this->search !== '' || $this->statusFilter !== 'pending',
        ])->layout('components.layouts.admin', [
            'title' => 'Comments',
        ]);
    }
}
