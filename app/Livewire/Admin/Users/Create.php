<?php

namespace App\Livewire\Admin\Users;

use App\Actions\AccessControl\AssignRole;
use App\Actions\Users\CreateUser;
use App\Enums\UserStatus;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class Create extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $status = 'active';

    /** @var list<int> */
    public array $selectedRoles = [];

    public function mount(): void
    {
        $this->authorize('create', User::class);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:254', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'status' => ['required', 'in:active,inactive'],
            'selectedRoles' => ['required', 'array', 'min:1'],
            'selectedRoles.*' => ['integer', Rule::exists('roles', 'id')],
        ];
    }

    public function save(): void
    {
        $this->authorize('create', User::class);

        $validated = $this->validate();

        $user = app(CreateUser::class)->handle(
            name: $validated['name'],
            email: $validated['email'],
            password: $validated['password'],
            status: UserStatus::from($validated['status']),
            actor: auth()->user(),
        );

        app(AssignRole::class)->handle(
            $user,
            $validated['selectedRoles'],
            auth()->user(),
        );

        \App\Support\Ui\Flash::success( 'User created.');

        $this->redirectRoute('admin.users.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.users.create', [
            'roles' => Role::query()->orderBy('name')->get(),
        ])->layout('components.layouts.admin', [
            'title' => 'Create user',
        ]);
    }
}
