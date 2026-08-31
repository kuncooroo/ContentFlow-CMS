<div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
    <p class="text-sm text-slate-500">{{ $post->author->name }}</p>
    <h2 class="mt-1 text-xl font-semibold text-slate-900">
        <a href="{{ route('public.posts.show', $post) }}" class="hover:text-slate-700">
            {{ $post->title }}
        </a>
    </h2>
    @if ($post->excerpt)
        <p class="mt-2 text-slate-600">{{ $post->excerpt }}</p>
    @endif
    @if ($post->publish_at)
        <p class="mt-3 text-xs text-slate-500">{{ $post->publish_at->format('M j, Y') }}</p>
    @endif
</div>
