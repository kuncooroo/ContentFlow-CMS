<div class="space-y-6">
<div class="flex items-center justify-between">
        <p class="text-sm text-slate-600">Manage administrative and editorial user accounts.</p>
        @can('create', App\Models\User::class)
            <a
                href="{{ route('admin.users.create') }}"
                class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800"
            >
                Create user
            </a>
        @endcan
    </div>

    <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="search" class="block text-sm font-medium text-slate-700">Search</label>
                <input id="search" type="search" wire:model.live.debounce.300ms="search" placeholder="Name or email" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label for="statusFilter" class="block text-sm font-medium text-slate-700">Status</label>
                <select id="statusFilter" wire:model.live="statusFilter" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                    <option value="">All statuses</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
        @if ($hasActiveFilters)
            <div class="mt-4">
                <button type="button" wire:click="clearFilters" class="text-sm font-medium text-slate-600 hover:text-slate-900">Clear filters</button>
            </div>
        @endif
    </div>

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Name</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Email</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Status</th>
                    <th class="px-4 py-3 text-right font-medium text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($users as $user)
                    <tr wire:key="user-{{ $user->id }}">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            @if ($user->status->isActive())
                                <span class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex rounded-full bg-slate-200 px-2.5 py-0.5 text-xs font-medium text-slate-700">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                @can('update', $user)
                                    <a
                                        href="{{ route('admin.users.edit', $user) }}"
                                        class="text-slate-600 hover:text-slate-900"
                                    >
                                        Edit
                                    </a>
                                @endcan

                                @can('changeStatus', $user)
                                    @if ($user->status->isActive())
                                        <button
                                            type="button"
                                            wire:click="deactivate({{ $user->id }})"
                                            wire:confirm="Deactivate this user? They will not be able to sign in."
                                            class="text-red-600 hover:text-red-800"
                                        >
                                            Deactivate
                                        </button>
                                    @else
                                        <button
                                            type="button"
                                            wire:click="activate({{ $user->id }})"
                                            class="text-slate-600 hover:text-slate-900"
                                        >
                                            Activate
                                        </button>
                                    @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-slate-500">
                            No users match your filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $users->links() }}
    </div>
</div>
