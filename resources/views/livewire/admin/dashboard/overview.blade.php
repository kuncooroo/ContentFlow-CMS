<div class="space-y-8">
    <div>
        <h2 class="text-lg font-semibold text-slate-900">Operational overview</h2>
        <p class="mt-1 text-sm text-slate-600">Content status counts and queues scoped to your permissions.</p>
    </div>

    @if ($summary->postCounts !== null)
        <section>
            <h3 class="text-sm font-semibold text-slate-900">Posts</h3>
            <div class="mt-3 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-slate-600">Draft</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $summary->postCounts['draft'] }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-slate-600">Scheduled</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $summary->postCounts['scheduled'] }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-slate-600">Published</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $summary->postCounts['published'] }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-slate-600">Archived</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $summary->postCounts['archived'] }}</p>
                </div>
            </div>
        </section>
    @endif

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @if ($summary->pageCounts !== null)
            <section class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-900">Pages</h3>
                <dl class="mt-3 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-slate-600">Draft</dt>
                        <dd class="font-medium text-slate-900">{{ $summary->pageCounts['draft'] }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-600">Published</dt>
                        <dd class="font-medium text-slate-900">{{ $summary->pageCounts['published'] }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-600">Archived</dt>
                        <dd class="font-medium text-slate-900">{{ $summary->pageCounts['archived'] }}</dd>
                    </div>
                </dl>
            </section>
        @endif

        @if ($summary->pendingComments !== null)
            <section class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-900">Comments</h3>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $summary->pendingComments }}</p>
                <p class="mt-1 text-sm text-slate-600">Pending moderation</p>
                @can('viewAny', App\Models\Comment::class)
                    <a href="{{ route('admin.comments.index') }}" class="mt-3 inline-flex text-sm font-medium text-slate-900 underline hover:no-underline">
                        Open moderation queue
                    </a>
                @endcan
            </section>
        @endif

        <section class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-900">Shortcuts</h3>
            <ul class="mt-3 space-y-2 text-sm">
                @can('create', App\Models\Post::class)
                    <li><a href="{{ route('admin.posts.create') }}" class="text-slate-700 hover:text-slate-900">Create post</a></li>
                @endcan
                @can('viewAny', App\Models\Post::class)
                    <li><a href="{{ route('admin.posts.index') }}" class="text-slate-700 hover:text-slate-900">Manage posts</a></li>
                @endcan
                @can('viewAny', App\Models\Page::class)
                    <li><a href="{{ route('admin.pages.index') }}" class="text-slate-700 hover:text-slate-900">Manage pages</a></li>
                @endcan
                @can('viewAny', App\Models\Media::class)
                    <li><a href="{{ route('admin.media.index') }}" class="text-slate-700 hover:text-slate-900">Media library</a></li>
                @endcan
            </ul>
        </section>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        @if ($summary->postCounts !== null)
            <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-4 py-3">
                    <h3 class="text-sm font-semibold text-slate-900">Recent posts</h3>
                </div>
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-slate-600">Title</th>
                            <th class="px-4 py-3 text-left font-medium text-slate-600">Status</th>
                            <th class="px-4 py-3 text-left font-medium text-slate-600">Updated</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($summary->recentPosts as $post)
                            <tr wire:key="recent-post-{{ $post->id }}">
                                <td class="px-4 py-3">
                                    @can('update', $post)
                                        <a href="{{ route('admin.posts.edit', $post) }}" class="font-medium text-slate-900 hover:underline">
                                            {{ $post->title }}
                                        </a>
                                    @else
                                        <span class="font-medium text-slate-900">{{ $post->title }}</span>
                                    @endcan
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $post->status->label() }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $post->updated_at?->timezone($siteTimezone)->format('M j, Y g:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-slate-500">No posts yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>

            <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-4 py-3">
                    <h3 class="text-sm font-semibold text-slate-900">Upcoming scheduled posts</h3>
                    <p class="text-xs text-slate-500">Times shown in {{ $siteTimezone }}</p>
                </div>
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-slate-600">Title</th>
                            <th class="px-4 py-3 text-left font-medium text-slate-600">Publish at</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($summary->scheduledPosts as $post)
                            <tr wire:key="scheduled-post-{{ $post->id }}">
                                <td class="px-4 py-3">
                                    @can('update', $post)
                                        <a href="{{ route('admin.posts.edit', $post) }}" class="font-medium text-slate-900 hover:underline">
                                            {{ $post->title }}
                                        </a>
                                    @else
                                        <span class="font-medium text-slate-900">{{ $post->title }}</span>
                                    @endcan
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $post->publish_at?->timezone($siteTimezone)->format('M j, Y g:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-8 text-center text-slate-500">No scheduled posts in the queue.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>
        @endif
    </div>

    @if ($summary->pageCounts !== null)
        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-4 py-3">
                <h3 class="text-sm font-semibold text-slate-900">Recent pages</h3>
            </div>
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-slate-600">Title</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-600">Status</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-600">Updated</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($summary->recentPages as $page)
                        <tr wire:key="recent-page-{{ $page->id }}">
                            <td class="px-4 py-3">
                                @can('update', $page)
                                    <a href="{{ route('admin.pages.edit', $page) }}" class="font-medium text-slate-900 hover:underline">
                                        {{ $page->title }}
                                    </a>
                                @else
                                    <span class="font-medium text-slate-900">{{ $page->title }}</span>
                                @endcan
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $page->status->label() }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $page->updated_at?->timezone($siteTimezone)->format('M j, Y g:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-slate-500">No pages yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    @endif
</div>
