<div class="space-y-6">
<div class="flex items-center justify-between">
        <p class="text-sm text-slate-600">Create and manage posts across draft, scheduled, and published states.</p>
        @can('create', App\Models\Post::class)
            <a
                href="{{ route('admin.posts.create') }}"
                class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800"
            >
                Create post
            </a>
        @endcan
    </div>

    <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <div class="grid gap-4 lg:grid-cols-2 xl:grid-cols-3">
            <div>
                <label for="search" class="block text-sm font-medium text-slate-700">Search</label>
                <input
                    id="search"
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Title or slug"
                    class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                >
            </div>
            <div>
                <label for="statusFilter" class="block text-sm font-medium text-slate-700">Status</label>
                <select id="statusFilter" wire:model.live="statusFilter" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>
            @if ($canFilterByAuthor)
                <div>
                    <label for="authorFilter" class="block text-sm font-medium text-slate-700">Author</label>
                    <select id="authorFilter" wire:model.live="authorFilter" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                        <option value="">All authors</option>
                        @foreach ($authors as $author)
                            <option value="{{ $author->id }}">{{ $author->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div>
                <label for="categoryFilter" class="block text-sm font-medium text-slate-700">Category</label>
                <select id="categoryFilter" wire:model.live="categoryFilter" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="createdFrom" class="block text-sm font-medium text-slate-700">Created from</label>
                <input id="createdFrom" type="date" wire:model.live="createdFrom" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label for="createdTo" class="block text-sm font-medium text-slate-700">Created to</label>
                <input id="createdTo" type="date" wire:model.live="createdTo" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
            </div>
        </div>
        @if ($hasActiveFilters)
            <div class="mt-4">
                <button type="button" wire:click="clearFilters" class="text-sm font-medium text-slate-600 hover:text-slate-900">
                    Clear filters
                </button>
            </div>
        @endif
    </div>

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Title</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Author</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Status</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Categories</th>
                    <th class="px-4 py-3 text-right font-medium text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($posts as $post)
                    <tr wire:key="post-{{ $post->id }}">
                        <td class="px-4 py-3">
                            <p class="font-medium text-slate-900">{{ $post->title }}</p>
                            <p class="text-xs text-slate-500">{{ $post->slug }}</p>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $post->author->name }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full bg-slate-200 px-2.5 py-0.5 text-xs font-medium text-slate-700">
                                {{ $post->status->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-600">
                            {{ $post->categories->pluck('name')->join(', ') ?: '—' }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            @can('update', $post)
                                <a href="{{ route('admin.posts.edit', $post) }}" class="text-slate-600 hover:text-slate-900">
                                    Edit
                                </a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                            No posts match your filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $posts->links() }}
    </div>
</div>
