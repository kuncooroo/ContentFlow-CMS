<x-layouts.public :seo="$seo">
    <div class="space-y-10">
        <header class="space-y-3">
            <p class="text-sm font-medium uppercase tracking-wider text-slate-500">Welcome</p>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">
                Latest posts
            </h1>
            <p class="max-w-2xl text-slate-600">
                Published articles from {{ app(\App\Services\Settings\SiteSettings::class)->siteName() }}.
            </p>
        </header>

        @if ($posts->isNotEmpty())
            <div class="grid gap-6 md:grid-cols-2">
                @foreach ($posts as $post)
                    @include('public.partials.post-card', ['post' => $post])
                @endforeach
            </div>

            <div>
                <a href="{{ route('public.posts.index') }}" class="text-sm font-medium text-slate-700 hover:text-slate-900">
                    View all posts &rarr;
                </a>
            </div>
        @else
            <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
                <p class="text-slate-600">No published posts yet. Check back soon.</p>
            </div>
        @endif
    </div>
</x-layouts.public>
