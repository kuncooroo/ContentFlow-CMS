<x-layouts.public :seo="$seo">
    <div class="space-y-8">
        <header class="space-y-2 border-b border-slate-200 pb-6">
            <p class="text-sm font-medium uppercase tracking-wider text-slate-500">Tag</p>
            <h1 class="text-3xl font-bold text-slate-900">{{ $tag->name }}</h1>
        </header>

        @if ($posts->isNotEmpty())
            <div class="grid gap-6 md:grid-cols-2">
                @foreach ($posts as $post)
                    @include('public.partials.post-card', ['post' => $post])
                @endforeach
            </div>

            <div>
                {{ $posts->links() }}
            </div>
        @else
            <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
                <p class="text-slate-600">No published posts with this tag yet.</p>
            </div>
        @endif
    </div>
</x-layouts.public>
