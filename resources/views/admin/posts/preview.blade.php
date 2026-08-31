<x-layouts.public title="Preview: {{ $post->title }}">
    <div class="mb-6 rounded-md border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
        Preview mode — this content is not publicly visible unless published.
        <a href="{{ route('admin.posts.edit', $post) }}" class="ml-2 font-medium underline">Back to editor</a>
    </div>

    <article class="space-y-6">
        <header class="space-y-2 border-b border-slate-200 pb-6">
            <p class="text-sm text-slate-500">
                Status: {{ $post->status->label() }}
                @if ($post->publish_at)
                    · Scheduled/published at {{ $post->publish_at->format('M j, Y g:i A') }}
                @endif
            </p>
            <h1 class="text-3xl font-bold text-slate-900">{{ $post->title }}</h1>
            @if ($post->excerpt)
                <p class="text-lg text-slate-600">{{ $post->excerpt }}</p>
            @endif
            <p class="text-sm text-slate-500">By {{ $post->author->name }}</p>
            @if ($post->categories->isNotEmpty())
                <p class="text-sm text-slate-500">
                    Categories: {{ $post->categories->pluck('name')->join(', ') }}
                </p>
            @endif
        </header>

        @if ($post->featuredMedia)
            <figure>
                <img
                    src="{{ $post->featuredMedia->url() }}"
                    alt="{{ $post->featuredMedia->alt_text ?? $post->title }}"
                    class="max-h-96 w-full rounded-lg object-cover"
                >
            </figure>
        @endif

        <div class="prose prose-slate max-w-none whitespace-pre-wrap text-slate-800">
            {{ $post->content }}
        </div>
    </article>
</x-layouts.public>
