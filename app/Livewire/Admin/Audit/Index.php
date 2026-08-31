<?php

namespace App\Livewire\Admin\Audit;

use App\Models\ActivityLog;
use App\Models\User;
use App\Queries\Audit\ActivityLogQuery;
use App\Support\Audit\ActivityEvent;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $eventFilter = '';

    public string $actorFilter = '';

    public string $createdFrom = '';

    public string $createdTo = '';

    public ?int $selectedLogId = null;

    public function mount(): void
    {
        $this->authorize('viewAny', ActivityLog::class);
    }

    public function updatedEventFilter(): void
    {
        $this->resetPage();
    }

    public function updatedActorFilter(): void
    {
        $this->resetPage();
    }

    public function updatedCreatedFrom(): void
    {
        $this->resetPage();
    }

    public function updatedCreatedTo(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['eventFilter', 'actorFilter', 'createdFrom', 'createdTo']);
        $this->resetPage();
    }

    public function showDetails(int $logId): void
    {
        $log = ActivityLog::query()->findOrFail($logId);

        $this->authorize('view', $log);

        $this->selectedLogId = $log->id;
    }

    public function closeDetails(): void
    {
        $this->selectedLogId = null;
    }

    public function render(): View
    {
        $query = app(ActivityLogQuery::class);

        $logs = $query->paginate(
            event: $this->eventFilter !== '' ? $this->eventFilter : null,
            actorUserId: $this->actorFilter !== '' ? (int) $this->actorFilter : null,
            createdFrom: $this->createdFrom !== '' ? $this->createdFrom : null,
            createdTo: $this->createdTo !== '' ? $this->createdTo : null,
        );

        $selectedLog = $this->selectedLogId !== null
            ? ActivityLog::query()->with('actor')->find($this->selectedLogId)
            : null;

        if ($selectedLog !== null) {
            $this->authorize('view', $selectedLog);
        }

        $actorIds = $query->actorIdsWithLogs();

        return view('livewire.admin.audit.index', [
            'logs' => $logs,
            'selectedLog' => $selectedLog,
            'events' => ActivityEvent::all(),
            'actors' => $actorIds !== []
                ? User::query()->whereIn('id', $actorIds)->orderBy('name')->get(['id', 'name'])
                : collect(),
            'hasActiveFilters' => $this->eventFilter !== ''
                || $this->actorFilter !== ''
                || $this->createdFrom !== ''
                || $this->createdTo !== '',
        ])->layout('components.layouts.admin', [
            'title' => 'Activity log',
        ]);
    }
}
