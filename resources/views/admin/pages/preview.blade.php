<x-layouts.public title="Preview: {{ $page->title }}">
    <div class="mb-6 rounded-md border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
        Preview mode — this page is not publicly visible unless published.
        <a href="{{ route('admin.pages.edit', $page) }}" class="ml-2 font-medium underline">Back to editor</a>
    </div>

    <article class="space-y-6">
        <header class="space-y-2 border-b border-slate-200 pb-6">
            <p class="text-sm text-slate-500">Status: {{ $page->status->label() }}</p>
            <h1 class="text-3xl font-bold text-slate-900">{{ $page->title }}</h1>
            <p class="text-sm text-slate-500">By {{ $page->author->name }}</p>
        </header>

        @if ($page->ogMedia)
            <figure>
                <img
                    src="{{ $page->ogMedia->url() }}"
                    alt="{{ $page->ogMedia->alt_text ?? $page->title }}"
                    class="max-h-96 w-full rounded-lg object-cover"
                >
            </figure>
        @endif

        <div class="prose prose-slate max-w-none whitespace-pre-wrap text-slate-800">
            {{ $page->content }}
        </div>
    </article>
</x-layouts.public>
