<div class="space-y-6">
    <div>
        <h2 class="text-lg font-semibold text-slate-900">Activity log</h2>
        <p class="mt-1 text-sm text-slate-600">Read-only audit trail of administrative and content actions.</p>
    </div>

    <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <div class="grid gap-4 lg:grid-cols-2 xl:grid-cols-4">
            <div>
                <label for="eventFilter" class="block text-sm font-medium text-slate-700">Event</label>
                <select id="eventFilter" wire:model.live="eventFilter" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                    <option value="">All events</option>
                    @foreach ($events as $event)
                        <option value="{{ $event }}">{{ \App\Support\Audit\ActivityLogPresenter::eventLabel($event) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="actorFilter" class="block text-sm font-medium text-slate-700">Actor</label>
                <select id="actorFilter" wire:model.live="actorFilter" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                    <option value="">All actors</option>
                    @foreach ($actors as $actor)
                        <option value="{{ $actor->id }}">{{ $actor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="createdFrom" class="block text-sm font-medium text-slate-700">From</label>
                <input id="createdFrom" type="date" wire:model.live="createdFrom" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label for="createdTo" class="block text-sm font-medium text-slate-700">To</label>
                <input id="createdTo" type="date" wire:model.live="createdTo" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
            </div>
        </div>
        @if ($hasActiveFilters)
            <div class="mt-4">
                <button type="button" wire:click="clearFilters" class="text-sm font-medium text-slate-600 hover:text-slate-900">
                    Clear filters
                </button>
            </div>
        @endif
    </div>

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">When</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Actor</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Event</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Subject</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Summary</th>
                    <th class="px-4 py-3 text-right font-medium text-slate-600">Details</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($logs as $log)
                    <tr wire:key="activity-log-{{ $log->id }}">
                        <td class="px-4 py-3 text-slate-600">{{ $log->created_at?->format('M j, Y g:i A') }}</td>
                        <td class="px-4 py-3 text-slate-900">{{ $log->actorLabel() }}</td>
                        <td class="px-4 py-3 text-slate-900">{{ \App\Support\Audit\ActivityLogPresenter::eventLabel($log->event) }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ \App\Support\Audit\ActivityLogPresenter::subjectLabel($log->subject_type, $log->subject_id) }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ \App\Support\Audit\ActivityLogPresenter::propertiesSummary($log->properties) }}</td>
                        <td class="px-4 py-3 text-right">
                            <button type="button" wire:click="showDetails({{ $log->id }})" class="text-slate-600 hover:text-slate-900">
                                View
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                            No activity logs match your filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $logs->links() }}
    </div>

    @if ($selectedLog)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4 py-8" wire:keydown.escape.window="closeDetails">
            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-lg border border-slate-200 bg-white shadow-xl">
                <div class="flex items-start justify-between border-b border-slate-200 px-6 py-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Activity details</h3>
                        <p class="mt-1 text-sm text-slate-600">{{ \App\Support\Audit\ActivityLogPresenter::eventLabel($selectedLog->event) }}</p>
                    </div>
                    <button type="button" wire:click="closeDetails" class="text-sm text-slate-600 hover:text-slate-900">
                        Close
                    </button>
                </div>
                <div class="space-y-4 px-6 py-4 text-sm">
                    <div>
                        <p class="font-medium text-slate-700">Timestamp</p>
                        <p class="mt-1 text-slate-900">{{ $selectedLog->created_at?->format('M j, Y g:i A') }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-slate-700">Actor</p>
                        <p class="mt-1 text-slate-900">{{ $selectedLog->actorLabel() }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-slate-700">Subject</p>
                        <p class="mt-1 text-slate-900">{{ \App\Support\Audit\ActivityLogPresenter::subjectLabel($selectedLog->subject_type, $selectedLog->subject_id) }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-slate-700">Properties</p>
                        <pre class="mt-2 overflow-x-auto rounded-md bg-slate-50 p-4 text-xs text-slate-800">{{ \App\Support\Audit\ActivityLogPresenter::propertiesForDisplay($selectedLog->properties) }}</pre>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
