<?php

namespace App\Livewire\Admin\Roles;

use App\Actions\AccessControl\SyncRolePermissions;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Edit extends Component
{
    public Role $role;

    /** @var list<int> */
    public array $selectedPermissions = [];

    public function mount(Role $role): void
    {
        $this->authorize('update', $role);

        $this->role = $role;
        $this->selectedPermissions = $role->permissions()->pluck('permissions.id')->all();
    }

    public function save(): void
    {
        $this->authorize('update', $this->role);

        try {
            app(SyncRolePermissions::class)->handle($this->role, $this->selectedPermissions, auth()->user());
        } catch (ValidationException $exception) {
            $this->setErrorBag($exception->validator->getMessageBag());

            return;
        }

        \App\Support\Ui\Flash::success( 'Role permissions updated.');

        $this->redirectRoute('admin.roles.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.roles.edit', [
            'permissions' => Permission::query()
                ->orderBy('group_name')
                ->orderBy('name')
                ->get()
                ->groupBy('group_name'),
        ])->layout('components.layouts.admin', [
            'title' => 'Edit role',
        ]);
    }
}
