<?php

namespace App\Actions\Navigation;

use App\Enums\MenuItemType;
use App\Models\Menu;
use App\Models\User;
use App\Services\Audit\ActivityLogger;
use App\Support\Audit\ActivityEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateMenuStructure
{
    private const MAX_ITEMS = 50;

    public function __construct(
        private readonly ActivityLogger $activityLogger,
    ) {}

    /**
     * @param  list<array{
     *     label: string,
     *     type: string,
     *     page_id?: int|null,
     *     post_id?: int|null,
     *     category_id?: int|null,
     *     custom_url?: string|null
     * }>  $items
     */
    public function handle(Menu $menu, array $items, User $actor): Menu
    {
        if (count($items) > self::MAX_ITEMS) {
            throw ValidationException::withMessages([
                'items' => 'A menu cannot contain more than '.self::MAX_ITEMS.' items.',
            ]);
        }

        $validatedItems = $this->validateItems($items);

        return DB::transaction(function () use ($menu, $validatedItems, $actor): Menu {
            $menu->items()->delete();

            foreach ($validatedItems as $position => $item) {
                $menu->items()->create([
                    'type' => $item['type'],
                    'label' => $item['label'],
                    'page_id' => $item['page_id'],
                    'post_id' => $item['post_id'],
                    'category_id' => $item['category_id'],
                    'custom_url' => $item['custom_url'],
                    'position' => $position,
                ]);
            }

            $this->activityLogger->record(
                $actor,
                ActivityEvent::MenuUpdated,
                $menu,
                [
                    'item_count' => count($validatedItems),
                ],
            );

            return $menu->fresh('items');
        });
    }

    /**
     * @param  list<array<string, mixed>>  $items
     * @return list<array{
     *     label: string,
     *     type: MenuItemType,
     *     page_id: ?int,
     *     post_id: ?int,
     *     category_id: ?int,
     *     custom_url: ?string
     * }>
     */
    private function validateItems(array $items): array
    {
        $validatedItems = [];

        foreach ($items as $index => $item) {
            $validated = validator($item, [
                'label' => ['required', 'string', 'max:150'],
                'type' => ['required', 'string', Rule::in(array_column(MenuItemType::cases(), 'value'))],
                'page_id' => ['nullable', 'integer', Rule::exists('pages', 'id')],
                'post_id' => ['nullable', 'integer', Rule::exists('posts', 'id')],
                'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')],
                'custom_url' => ['nullable', 'string', 'max:2048'],
            ])->validate();

            $type = MenuItemType::from($validated['type']);
            $normalized = [
                'label' => $validated['label'],
                'type' => $type,
                'page_id' => null,
                'post_id' => null,
                'category_id' => null,
                'custom_url' => null,
            ];

            match ($type) {
                MenuItemType::Page => $this->assertTargetId($normalized, 'page_id', $validated['page_id'] ?? null, $index),
                MenuItemType::Post => $this->assertTargetId($normalized, 'post_id', $validated['post_id'] ?? null, $index),
                MenuItemType::Category => $this->assertTargetId($normalized, 'category_id', $validated['category_id'] ?? null, $index),
                MenuItemType::Custom => $this->assertCustomUrl($normalized, $validated['custom_url'] ?? null, $index),
            };

            $validatedItems[] = $normalized;
        }

        return $validatedItems;
    }

    /**
     * @param  array{
     *     label: string,
     *     type: MenuItemType,
     *     page_id: ?int,
     *     post_id: ?int,
     *     category_id: ?int,
     *     custom_url: ?string
     * }  $normalized
     */
    private function assertTargetId(array &$normalized, string $field, ?int $value, int $index): void
    {
        if ($value === null) {
            throw ValidationException::withMessages([
                "items.{$index}.{$field}" => 'A target is required for this menu item type.',
            ]);
        }

        $normalized[$field] = $value;
    }

    /**
     * @param  array{
     *     label: string,
     *     type: MenuItemType,
     *     page_id: ?int,
     *     post_id: ?int,
     *     category_id: ?int,
     *     custom_url: ?string
     * }  $normalized
     */
    private function assertCustomUrl(array &$normalized, ?string $url, int $index): void
    {
        if ($url === null || trim($url) === '') {
            throw ValidationException::withMessages([
                "items.{$index}.custom_url" => 'A custom URL is required for this menu item type.',
            ]);
        }

        $url = trim($url);

        if (preg_match('/^\s*(javascript|data|vbscript):/i', $url)) {
            throw ValidationException::withMessages([
                "items.{$index}.custom_url" => 'This URL scheme is not allowed.',
            ]);
        }

        $isAbsoluteUrl = filter_var($url, FILTER_VALIDATE_URL) !== false;
        $isRelativePath = (bool) preg_match('#^/[a-zA-Z0-9\-_./?=&%]*$#', $url);

        if (! $isAbsoluteUrl && ! $isRelativePath) {
            throw ValidationException::withMessages([
                "items.{$index}.custom_url" => 'Enter a valid URL or site path starting with /.',
            ]);
        }

        $normalized['custom_url'] = $url;
    }
}
