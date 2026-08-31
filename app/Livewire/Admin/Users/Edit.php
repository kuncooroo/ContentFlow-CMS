<?php

namespace App\Livewire\Admin\Users;

use App\Actions\AccessControl\AssignRole;
use App\Actions\Users\UpdateUser;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Edit extends Component
{
    public User $user;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    /** @var list<int> */
    public array $selectedRoles = [];

    public function mount(User $user): void
    {
        $this->authorize('update', $user);

        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->selectedRoles = $user->roles()->pluck('roles.id')->all();
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => [
                'required',
                'string',
                'email',
                'max:254',
                Rule::unique('users', 'email')->ignore($this->user->id),
            ],
            'password' => ['nullable', 'string', 'confirmed', Password::defaults()],
            'selectedRoles' => ['required', 'array', 'min:1'],
            'selectedRoles.*' => ['integer', Rule::exists('roles', 'id')],
        ];
    }

    public function save(): void
    {
        $this->authorize('update', $this->user);

        $validated = $this->validate();

        try {
            app(UpdateUser::class)->handle($this->user, [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'] ?: null,
            ]);

            app(AssignRole::class)->handle(
                $this->user,
                $validated['selectedRoles'],
                auth()->user(),
            );
        } catch (ValidationException $exception) {
            $this->setErrorBag($exception->validator->getMessageBag());

            return;
        }

        \App\Support\Ui\Flash::success( 'User updated.');

        $this->redirectRoute('admin.users.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.users.edit', [
            'roles' => Role::query()->orderBy('name')->get(),
        ])->layout('components.layouts.admin', [
            'title' => 'Edit user',
        ]);
    }
}
