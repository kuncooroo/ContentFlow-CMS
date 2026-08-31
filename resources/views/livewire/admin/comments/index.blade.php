<div class="space-y-6">
@error('moderation')
        <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ $message }}
        </div>
    @enderror

    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm text-slate-600">Review and moderate public comments.</p>
            <p class="text-xs text-slate-500">{{ $pendingCount }} pending comment(s)</p>
        </div>
        <div class="flex flex-wrap items-end gap-3">
            <div>
                <label for="search" class="block text-sm font-medium text-slate-700">Search</label>
                <input
                    id="search"
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Author, email, content, or post"
                    class="mt-1 block w-full min-w-64 rounded-md border border-slate-300 px-3 py-2 text-sm"
                >
            </div>
            <div>
                <label for="statusFilter" class="block text-sm font-medium text-slate-700">Status</label>
                <select
                    id="statusFilter"
                    wire:model.live="statusFilter"
                    class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                >
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="spam">Spam</option>
                    <option value="rejected">Rejected</option>
                    <option value="all">All</option>
                </select>
            </div>
        </div>
    </div>

    @if ($hasActiveFilters)
        <button type="button" wire:click="clearFilters" class="text-sm font-medium text-slate-600 hover:text-slate-900">
            Clear filters
        </button>
    @endif

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Comment</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Post</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Status</th>
                    <th class="px-4 py-3 text-right font-medium text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($comments as $comment)
                    <tr wire:key="comment-{{ $comment->id }}">
                        <td class="px-4 py-3">
                            <p class="font-medium text-slate-900">{{ $comment->author_name }}</p>
                            <p class="text-xs text-slate-500">{{ $comment->author_email }}</p>
                            <p class="mt-2 whitespace-pre-wrap text-slate-700">{{ $comment->content }}</p>
                        </td>
                        <td class="px-4 py-3 text-slate-600">
                            <p>{{ $comment->post->title }}</p>
                            <p class="text-xs text-slate-500">{{ $comment->created_at->format('M j, Y g:i A') }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full bg-slate-200 px-2.5 py-0.5 text-xs font-medium text-slate-700">
                                {{ $comment->status->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @can('moderate', $comment)
                                <div class="flex flex-wrap justify-end gap-2">
                                    @if ($comment->status->canTransitionTo(\App\Enums\CommentStatus::Approved))
                                        <button
                                            type="button"
                                            wire:click="moderate({{ $comment->id }}, 'approved')"
                                            class="rounded-md bg-green-700 px-2 py-1 text-xs font-medium text-white hover:bg-green-800"
                                        >
                                            Approve
                                        </button>
                                    @endif
                                    @if ($comment->status->canTransitionTo(\App\Enums\CommentStatus::Spam))
                                        <button
                                            type="button"
                                            wire:click="moderate({{ $comment->id }}, 'spam')"
                                            class="rounded-md border border-slate-300 px-2 py-1 text-xs font-medium text-slate-700 hover:bg-slate-50"
                                        >
                                            Spam
                                        </button>
                                    @endif
                                    @if ($comment->status->canTransitionTo(\App\Enums\CommentStatus::Rejected))
                                        <button
                                            type="button"
                                            wire:click="moderate({{ $comment->id }}, 'rejected')"
                                            class="rounded-md border border-red-300 px-2 py-1 text-xs font-medium text-red-700 hover:bg-red-50"
                                        >
                                            Reject
                                        </button>
                                    @endif
                                </div>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-slate-500">
                            No comments match your filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $comments->links() }}
    </div>
</div>
