<x-layouts.public :seo="$seo">
    <article class="space-y-8">
        <header class="space-y-3 border-b border-slate-200 pb-6">
            <h1 class="text-3xl font-bold text-slate-900">{{ $page->title }}</h1>
        </header>

        <x-rich-content :content="$page->content" />
    </article>
</x-layouts.public>
