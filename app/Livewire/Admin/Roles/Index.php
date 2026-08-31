<?php

namespace App\Livewire\Admin\Roles;

use App\Models\Role;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Index extends Component
{
    public function mount(): void
    {
        $this->authorize('viewAny', Role::class);
    }

    public function render(): View
    {
        return view('livewire.admin.roles.index', [
            'roles' => Role::query()
                ->withCount('permissions')
                ->orderBy('name')
                ->get(),
        ])->layout('components.layouts.admin', [
            'title' => 'Roles',
        ]);
    }
}
