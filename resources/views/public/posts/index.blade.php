<x-layouts.public :seo="$seo">
    <div class="space-y-8">
        <header class="space-y-2 border-b border-slate-200 pb-6">
            <h1 class="text-3xl font-bold text-slate-900">Blog</h1>
            <p class="text-slate-600">All published posts.</p>
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
                <p class="text-slate-600">No published posts yet.</p>
            </div>
        @endif
    </div>
</x-layouts.public>
