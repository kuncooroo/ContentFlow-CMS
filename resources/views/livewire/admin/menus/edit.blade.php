<div class="mx-auto max-w-4xl space-y-6">
<div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-sm text-slate-600">
            Editing <span class="font-medium text-slate-900">{{ $menu->name }}</span>
            <span class="text-slate-500">({{ $menu->key }})</span>
        </p>
    </div>

    @error('items')
        <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ $message }}
        </div>
    @enderror

    <form wire:submit="save" class="space-y-4">
        @forelse ($items as $index => $item)
            <div wire:key="menu-item-{{ $index }}" class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Label</label>
                        <input
                            type="text"
                            wire:model="items.{{ $index }}.label"
                            class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                        >
                        @error('items.'.$index.'.label')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Type</label>
                        <select
                            wire:model.live="items.{{ $index }}.type"
                            class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                        >
                            @foreach ($types as $type)
                                <option value="{{ $type->value }}">{{ $type->label() }}</option>
                            @endforeach
                        </select>
                        @error('items.'.$index.'.type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    @if ($item['type'] === 'page')
                        <label class="block text-sm font-medium text-slate-700">Page</label>
                        <select wire:model="items.{{ $index }}.page_id" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                            <option value="">Select page</option>
                            @foreach ($pages as $page)
                                <option value="{{ $page->id }}">{{ $page->title }}</option>
                            @endforeach
                        </select>
                        @error('items.'.$index.'.page_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    @elseif ($item['type'] === 'post')
                        <label class="block text-sm font-medium text-slate-700">Post</label>
                        <select wire:model="items.{{ $index }}.post_id" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                            <option value="">Select post</option>
                            @foreach ($posts as $post)
                                <option value="{{ $post->id }}">{{ $post->title }}</option>
                            @endforeach
                        </select>
                        @error('items.'.$index.'.post_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    @elseif ($item['type'] === 'category')
                        <label class="block text-sm font-medium text-slate-700">Category</label>
                        <select wire:model="items.{{ $index }}.category_id" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                            <option value="">Select category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('items.'.$index.'.category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    @else
                        <label class="block text-sm font-medium text-slate-700">Custom URL</label>
                        <input
                            type="text"
                            wire:model="items.{{ $index }}.custom_url"
                            placeholder="/about or https://example.com"
                            class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                        >
                        @error('items.'.$index.'.custom_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    @endif
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <button type="button" wire:click="moveUp({{ $index }})" class="rounded-md border border-slate-300 px-2 py-1 text-xs text-slate-700 hover:bg-slate-50">
                        Move up
                    </button>
                    <button type="button" wire:click="moveDown({{ $index }})" class="rounded-md border border-slate-300 px-2 py-1 text-xs text-slate-700 hover:bg-slate-50">
                        Move down
                    </button>
                    <button type="button" wire:click="removeItem({{ $index }})" class="rounded-md border border-red-300 px-2 py-1 text-xs text-red-700 hover:bg-red-50">
                        Remove
                    </button>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-500">No menu items yet. Add one below.</p>
        @endforelse

        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex gap-2">
                <button type="button" wire:click="addItem" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Add item
                </button>
                <a href="{{ route('admin.menus.index') }}" class="rounded-md px-4 py-2 text-sm text-slate-600 hover:text-slate-900">
                    Cancel
                </a>
            </div>
            <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                Save menu
            </button>
        </div>
    </form>
</div>
