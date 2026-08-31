<div class="space-y-6">
@error('page')
        <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ $message }}
        </div>
    @enderror

    <div class="flex items-center justify-between">
        <p class="text-sm text-slate-600">Manage static pages for your site.</p>
        @can('create', App\Models\Page::class)
            <a
                href="{{ route('admin.pages.create') }}"
                class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800"
            >
                Create page
            </a>
        @endcan
    </div>

    <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <div class="grid gap-4 lg:grid-cols-2 xl:grid-cols-3">
            <div>
                <label for="search" class="block text-sm font-medium text-slate-700">Search</label>
                <input id="search" type="search" wire:model.live.debounce.300ms="search" placeholder="Title or slug" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
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
            <div>
                <label for="authorFilter" class="block text-sm font-medium text-slate-700">Author</label>
                <select id="authorFilter" wire:model.live="authorFilter" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                    <option value="">All authors</option>
                    @foreach ($authors as $author)
                        <option value="{{ $author->id }}">{{ $author->name }}</option>
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
                <button type="button" wire:click="clearFilters" class="text-sm font-medium text-slate-600 hover:text-slate-900">Clear filters</button>
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
                    <th class="px-4 py-3 text-right font-medium text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($pages as $page)
                    <tr wire:key="page-{{ $page->id }}">
                        <td class="px-4 py-3">
                            <p class="font-medium text-slate-900">{{ $page->title }}</p>
                            <p class="text-xs text-slate-500">{{ $page->slug }}</p>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $page->author->name }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full bg-slate-200 px-2.5 py-0.5 text-xs font-medium text-slate-700">
                                {{ $page->status->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-3">
                            @can('update', $page)
                                <a href="{{ route('admin.pages.edit', $page) }}" class="text-slate-600 hover:text-slate-900">
                                    Edit
                                </a>
                            @endcan
                            @can('delete', $page)
                                <button
                                    type="button"
                                    wire:click="delete({{ $page->id }})"
                                    wire:confirm="Delete this page permanently?"
                                    class="text-red-600 hover:text-red-800"
                                >
                                    Delete
                                </button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-slate-500">
                            No pages match your filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $pages->links() }}
    </div>
</div>
