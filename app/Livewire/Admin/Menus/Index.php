<?php

namespace App\Livewire\Admin\Menus;

use App\Actions\Navigation\CreateMenu;
use App\Models\Menu;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Index extends Component
{
    public string $newMenuKey = '';

    public string $newMenuName = '';

    public function mount(): void
    {
        $this->authorize('viewAny', Menu::class);
    }

    public function createMenu(): void
    {
        $this->authorize('create', Menu::class);

        try {
            app(CreateMenu::class)->handle($this->newMenuKey, $this->newMenuName);
        } catch (ValidationException $exception) {
            $this->setErrorBag($exception->validator->getMessageBag());

            return;
        }

        $this->reset('newMenuKey', 'newMenuName');
        \App\Support\Ui\Flash::success( 'Menu created.');
    }

    public function render(): View
    {
        return view('livewire.admin.menus.index', [
            'menus' => Menu::query()->orderBy('name')->get(),
        ])->layout('components.layouts.admin', [
            'title' => 'Menus',
        ]);
    }
}
