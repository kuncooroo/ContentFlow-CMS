<x-layouts.public :seo="$seo">
    <article class="space-y-8">
        <header class="space-y-3 border-b border-slate-200 pb-6">
            <p class="text-sm text-slate-500">By {{ $post->author->name }}</p>
            <h1 class="text-3xl font-bold text-slate-900">{{ $post->title }}</h1>
            @if ($post->excerpt)
                <p class="text-lg text-slate-600">{{ $post->excerpt }}</p>
            @endif
        </header>

        <x-rich-content :content="$post->content" />

        <section class="space-y-6 border-t border-slate-200 pt-8">
            <h2 class="text-xl font-semibold text-slate-900">Comments</h2>

            @if ($commentsEnabled)
                <form method="POST" action="{{ route('public.comments.store', $post) }}" class="space-y-4 rounded-lg border border-slate-200 bg-slate-50 p-4">
                    @csrf
                    <div class="hidden" aria-hidden="true">
                        <label for="website">Website</label>
                        <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="author_name" class="block text-sm font-medium text-slate-700">Name</label>
                            <input
                                id="author_name"
                                name="author_name"
                                type="text"
                                value="{{ old('author_name') }}"
                                required
                                class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                            >
                            @error('author_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="author_email" class="block text-sm font-medium text-slate-700">Email</label>
                            <input
                                id="author_email"
                                name="author_email"
                                type="email"
                                value="{{ old('author_email') }}"
                                required
                                class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                            >
                            @error('author_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="content" class="block text-sm font-medium text-slate-700">Comment</label>
                        <textarea
                            id="content"
                            name="content"
                            rows="4"
                            required
                            class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                        >{{ old('content') }}</textarea>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <button
                        type="submit"
                        class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800"
                    >
                        Submit comment
                    </button>
                </form>
            @else
                <p class="text-sm text-slate-600">Comments are currently disabled.</p>
            @endif

            <div class="space-y-4">
                @forelse ($post->approvedComments as $comment)
                    <div class="rounded-lg border border-slate-200 bg-white p-4">
                        <p class="text-sm font-medium text-slate-900">{{ $comment->author_name }}</p>
                        <p class="mt-2 whitespace-pre-wrap text-sm text-slate-700">{{ $comment->content }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No approved comments yet.</p>
                @endforelse
            </div>
        </section>
    </article>
</x-layouts.public>
