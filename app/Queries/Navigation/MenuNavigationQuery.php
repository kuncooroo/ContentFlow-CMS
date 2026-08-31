<?php

namespace App\Queries\Navigation;

use App\Models\Menu;
use Illuminate\Support\Collection;

class MenuNavigationQuery
{
    /**
     * @return Collection<int, array{
     *     label: string,
     *     type: string,
     *     url: ?string,
     *     is_publicly_visible: bool
     * }>
     */
    public function publicItemsForMenu(string $menuKey): Collection
    {
        $menu = Menu::query()
            ->where('key', $menuKey)
            ->with(['items' => fn ($query) => $query->with(['page', 'post', 'category'])])
            ->first();

        if ($menu === null) {
            return collect();
        }

        return $menu->items
            ->map(fn ($item) => [
                'label' => $item->label,
                'type' => $item->type->value,
                'url' => $item->resolvedUrl(),
                'is_publicly_visible' => $item->isPubliclyVisibleTarget(),
            ])
            ->values();
    }
}
