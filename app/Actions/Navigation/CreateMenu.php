<?php

namespace App\Actions\Navigation;

use App\Models\Menu;
use Illuminate\Validation\Rule;

class CreateMenu
{
    public function handle(string $key, string $name): Menu
    {
        $validated = validator([
            'key' => $key,
            'name' => $name,
        ], [
            'key' => ['required', 'string', 'max:80', 'regex:/^[a-z0-9]+(?:_[a-z0-9]+)*$/', 'unique:menus,key'],
            'name' => ['required', 'string', 'max:120'],
        ])->validate();

        return Menu::query()->create($validated);
    }
}
