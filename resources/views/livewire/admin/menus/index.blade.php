<div class="space-y-6">
<div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-sm font-semibold text-slate-900">Create menu</h2>
        <form wire:submit="createMenu" class="mt-4 grid gap-4 md:grid-cols-3">
            <div>
                <label for="newMenuKey" class="block text-sm font-medium text-slate-700">Key</label>
                <input
                    id="newMenuKey"
                    type="text"
                    wire:model="newMenuKey"
                    placeholder="primary"
                    class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                >
                @error('key')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="newMenuName" class="block text-sm font-medium text-slate-700">Name</label>
                <input
                    id="newMenuName"
                    type="text"
                    wire:model="newMenuName"
                    placeholder="Primary Navigation"
                    class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                >
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-end">
                @can('create', App\Models\Menu::class)
                    <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                        Create menu
                    </button>
                @endcan
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Name</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Key</th>
                    <th class="px-4 py-3 text-right font-medium text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($menus as $menu)
                    <tr wire:key="menu-{{ $menu->id }}">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $menu->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $menu->key }}</td>
                        <td class="px-4 py-3 text-right">
                            @can('update', $menu)
                                <a href="{{ route('admin.menus.edit', $menu) }}" class="text-slate-600 hover:text-slate-900">
                                    Edit items
                                </a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-slate-500">
                            No menus yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
