<div class="space-y-6">
@error('category')
        <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ $message }}
        </div>
    @enderror

    <div class="flex items-center justify-between">
        <p class="text-sm text-slate-600">Manage post categories and their public slugs.</p>
        @can('create', App\Models\Category::class)
            <a
                href="{{ route('admin.categories.create') }}"
                class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800"
            >
                Create category
            </a>
        @endcan
    </div>

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Name</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Slug</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Description</th>
                    <th class="px-4 py-3 text-right font-medium text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($categories as $category)
                    <tr wire:key="category-{{ $category->id }}">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $category->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $category->slug }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ \Illuminate\Support\Str::limit($category->description, 80) }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                @can('update', $category)
                                    <a
                                        href="{{ route('admin.categories.edit', $category) }}"
                                        class="text-slate-600 hover:text-slate-900"
                                    >
                                        Edit
                                    </a>
                                @endcan

                                @can('delete', $category)
                                    <button
                                        type="button"
                                        wire:click="delete({{ $category->id }})"
                                        wire:confirm="Delete this category? This cannot be undone."
                                        class="text-red-600 hover:text-red-800"
                                    >
                                        Delete
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-slate-500">
                            No categories found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $categories->links() }}
    </div>
</div>
