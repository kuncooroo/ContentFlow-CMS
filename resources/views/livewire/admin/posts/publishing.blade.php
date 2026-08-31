@php
    use App\Enums\PostStatus;
@endphp

<div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm text-slate-600">
                Status:
                <span class="font-medium text-slate-900">{{ $post->status->label() }}</span>
            </p>
            @if ($post->status === PostStatus::Scheduled && $post->publish_at)
                <p class="mt-1 text-xs text-slate-500">
                    Scheduled for {{ $post->publish_at->format('M j, Y g:i A') }}
                </p>
            @elseif ($post->status === PostStatus::Published && $post->publish_at)
                <p class="mt-1 text-xs text-slate-500">
                    Published {{ $post->publish_at->format('M j, Y g:i A') }}
                </p>
            @endif
        </div>

        @can('preview', $post)
            <a
                href="{{ route('admin.posts.preview', $post) }}"
                target="_blank"
                class="text-sm font-medium text-slate-600 hover:text-slate-900"
            >
                Preview
            </a>
        @endcan
    </div>

    @error('status')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
    @error('post')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
    @error('publish_at')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror

    <div class="mt-4 flex flex-wrap gap-2">
        @if (in_array($post->status, [PostStatus::Draft, PostStatus::Scheduled], true))
            @can('publish', $post)
                <button
                    type="button"
                    wire:click="publish"
                    class="rounded-md bg-green-700 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-800"
                >
                    Publish now
                </button>
            @endcan
        @endif

        @if ($post->status === PostStatus::Draft)
            @can('schedule', $post)
                <div class="flex flex-wrap items-center gap-2">
                    <input
                        type="datetime-local"
                        wire:model="scheduledAt"
                        class="rounded-md border border-slate-300 px-2 py-1.5 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
                    >
                    <button
                        type="button"
                        wire:click="schedule"
                        class="rounded-md bg-blue-700 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-800"
                    >
                        Schedule
                    </button>
                </div>
            @endcan
        @endif

        @if ($post->status === PostStatus::Scheduled)
            @can('cancelSchedule', $post)
                <button
                    type="button"
                    wire:click="cancelSchedule"
                    wire:confirm="Cancel the scheduled publication and return this post to draft?"
                    class="rounded-md border border-slate-300 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                    Cancel schedule
                </button>
            @endcan
        @endif

        @if ($post->status === PostStatus::Published)
            @can('archive', $post)
                <button
                    type="button"
                    wire:click="archive"
                    wire:confirm="Archive this post? It will be removed from public listings."
                    class="rounded-md border border-slate-300 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                    Archive
                </button>
            @endcan

            @can('unpublish', $post)
                <button
                    type="button"
                    wire:click="unpublish"
                    wire:confirm="Unpublish this post and return it to draft?"
                    class="rounded-md border border-amber-300 bg-amber-50 px-3 py-1.5 text-sm font-medium text-amber-900 hover:bg-amber-100"
                >
                    Unpublish to draft
                </button>
            @endcan
        @endif

        @if ($post->status === PostStatus::Archived)
            @can('restore', $post)
                <button
                    type="button"
                    wire:click="restoreToDraft"
                    class="rounded-md bg-slate-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-slate-800"
                >
                    Restore to draft
                </button>
            @endcan
        @endif
    </div>
</div>
