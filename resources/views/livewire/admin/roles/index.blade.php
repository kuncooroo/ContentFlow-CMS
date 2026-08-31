<div class="space-y-6">
<p class="text-sm text-slate-600">Default roles and their permission assignments.</p>

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Role</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Description</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Permissions</th>
                    <th class="px-4 py-3 text-right font-medium text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach ($roles as $role)
                    <tr wire:key="role-{{ $role->id }}">
                        <td class="px-4 py-3 font-medium text-slate-900">
                            {{ $role->name }}
                            @if ($role->is_system)
                                <span class="ml-2 text-xs font-normal text-slate-500">(system)</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $role->description }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $role->permissions_count }}</td>
                        <td class="px-4 py-3 text-right">
                            @can('update', $role)
                                <a
                                    href="{{ route('admin.roles.edit', $role) }}"
                                    class="text-slate-600 hover:text-slate-900"
                                >
                                    Edit permissions
                                </a>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
