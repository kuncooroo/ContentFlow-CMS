<?php

namespace App\Livewire\Admin\Menus;

use App\Actions\Navigation\UpdateMenuStructure;
use App\Enums\MenuItemType;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Edit extends Component
{
    public Menu $menu;

    /** @var list<array{
     *     label: string,
     *     type: string,
     *     page_id: ?int,
     *     post_id: ?int,
     *     category_id: ?int,
     *     custom_url: ?string
     * }> */
    public array $items = [];

    public function mount(Menu $menu): void
    {
        $this->authorize('update', $menu);

        $this->menu = $menu->load('items');
        $this->items = $menu->items->map(fn ($item) => [
            'label' => $item->label,
            'type' => $item->type->value,
            'page_id' => $item->page_id,
            'post_id' => $item->post_id,
            'category_id' => $item->category_id,
            'custom_url' => $item->custom_url,
        ])->all();
    }

    public function addItem(): void
    {
        $this->items[] = [
            'label' => '',
            'type' => MenuItemType::Custom->value,
            'page_id' => null,
            'post_id' => null,
            'category_id' => null,
            'custom_url' => '/',
        ];
    }

    public function removeItem(int $index): void
    {
        if (! isset($this->items[$index])) {
            return;
        }

        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function moveUp(int $index): void
    {
        if ($index <= 0 || ! isset($this->items[$index])) {
            return;
        }

        [$this->items[$index - 1], $this->items[$index]] = [$this->items[$index], $this->items[$index - 1]];
    }

    public function moveDown(int $index): void
    {
        if (! isset($this->items[$index], $this->items[$index + 1])) {
            return;
        }

        [$this->items[$index + 1], $this->items[$index]] = [$this->items[$index], $this->items[$index + 1]];
    }

    public function save(): void
    {
        $this->authorize('update', $this->menu);

        try {
            app(UpdateMenuStructure::class)->handle(
                $this->menu,
                $this->items,
                auth()->user(),
            );
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->setErrorBag($exception->validator->getMessageBag());

            return;
        }

        \App\Support\Ui\Flash::success( 'Menu structure saved.');
        $this->redirectRoute('admin.menus.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.menus.edit', [
            'pages' => Page::query()->orderBy('title')->get(['id', 'title']),
            'posts' => Post::query()->orderBy('title')->get(['id', 'title']),
            'categories' => Category::query()->orderBy('name')->get(['id', 'name']),
            'types' => MenuItemType::cases(),
        ])->layout('components.layouts.admin', [
            'title' => 'Edit menu',
        ]);
    }
}
