<div class="mx-auto max-w-4xl space-y-6">
@if ($showPublishingActions && $post)
        @include('livewire.admin.posts.publishing', ['post' => $post])
    @else
        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-slate-600">
                Status:
                <span class="font-medium text-slate-900">{{ $statusLabel }}</span>
            </p>
        </div>
    @endif

    <form wire:submit="save" class="space-y-6 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <div>
            <label for="title" class="block text-sm font-medium text-slate-700">Title</label>
            <input
                id="title"
                type="text"
                wire:model.live="title"
                class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
            >
            @error('title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="slug" class="block text-sm font-medium text-slate-700">Slug</label>
            <input
                id="slug"
                type="text"
                wire:model.live="slug"
                class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
            >
            @error('slug')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="excerpt" class="block text-sm font-medium text-slate-700">Excerpt</label>
            <textarea
                id="excerpt"
                wire:model="excerpt"
                rows="3"
                class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
            ></textarea>
            @error('excerpt')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="content" class="block text-sm font-medium text-slate-700">Body</label>
            <textarea
                id="content"
                wire:model="content"
                rows="12"
                class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
            ></textarea>
            @error('content')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="featured_media_id" class="block text-sm font-medium text-slate-700">Featured image</label>
            <select
                id="featured_media_id"
                wire:model="featured_media_id"
                class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
            >
                <option value="">None</option>
                @foreach ($mediaItems as $media)
                    <option value="{{ $media->id }}">{{ $media->original_name }}</option>
                @endforeach
            </select>
            @error('featured_media_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div>
                <p class="block text-sm font-medium text-slate-700">Categories</p>
                <div class="mt-2 max-h-48 space-y-2 overflow-y-auto rounded-md border border-slate-200 p-3">
                    @forelse ($categories as $category)
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input
                                type="checkbox"
                                wire:model="selectedCategories"
                                value="{{ $category->id }}"
                                class="rounded border-slate-300 text-slate-900 focus:ring-slate-500"
                            >
                            {{ $category->name }}
                        </label>
                    @empty
                        <p class="text-sm text-slate-500">No categories yet.</p>
                    @endforelse
                </div>
                @error('category_ids')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <p class="block text-sm font-medium text-slate-700">Tags</p>
                <div class="mt-2 max-h-48 space-y-2 overflow-y-auto rounded-md border border-slate-200 p-3">
                    @forelse ($tags as $tag)
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input
                                type="checkbox"
                                wire:model="selectedTags"
                                value="{{ $tag->id }}"
                                class="rounded border-slate-300 text-slate-900 focus:ring-slate-500"
                            >
                            {{ $tag->name }}
                        </label>
                    @empty
                        <p class="text-sm text-slate-500">No tags yet.</p>
                    @endforelse
                </div>
                @error('tag_ids')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        @include('livewire.admin.partials.seo-fields')

        <div class="flex items-center justify-between">
            <a href="{{ route('admin.posts.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Cancel</a>
            <button
                type="submit"
                class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800"
            >
                {{ $submitLabel }}
            </button>
        </div>
    </form>
</div>
