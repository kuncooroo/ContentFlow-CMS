<?php

namespace App\Livewire\Admin\Users;

use App\Actions\Users\ChangeUserStatus;
use App\Enums\UserStatus;
use App\Models\User;
use App\Queries\Users\UserIndexQuery;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public function mount(): void
    {
        $this->authorize('viewAny', User::class);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'statusFilter']);
        $this->resetPage();
    }

    public function activate(int $userId): void
    {
        $user = User::query()->findOrFail($userId);

        $this->authorize('changeStatus', $user);

        try {
            app(ChangeUserStatus::class)->handle(
                $user,
                UserStatus::Active,
                auth()->user(),
            );
        } catch (ValidationException $exception) {
            $this->addError('demo', $exception->validator->errors()->first());

            return;
        }

        \App\Support\Ui\Flash::success( 'User activated.');
    }

    public function deactivate(int $userId): void
    {
        $user = User::query()->findOrFail($userId);

        $this->authorize('changeStatus', $user);

        try {
            app(ChangeUserStatus::class)->handle(
                $user,
                UserStatus::Inactive,
                auth()->user(),
            );
        } catch (ValidationException $exception) {
            $this->addError('demo', $exception->validator->errors()->first());

            return;
        }

        \App\Support\Ui\Flash::success( 'User deactivated.');
    }

    public function render(): View
    {
        $status = $this->statusFilter !== ''
            ? UserStatus::from($this->statusFilter)
            : null;

        return view('livewire.admin.users.index', [
            'users' => app(UserIndexQuery::class)->paginate(
                search: $this->search !== '' ? $this->search : null,
                status: $status,
            ),
            'hasActiveFilters' => $this->search !== '' || $this->statusFilter !== '',
        ])->layout('components.layouts.admin', [
            'title' => 'Users',
        ]);
    }
}
