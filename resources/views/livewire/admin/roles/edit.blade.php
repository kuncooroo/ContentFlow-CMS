<div class="mx-auto max-w-3xl space-y-6">
    <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900">{{ $role->name }}</h2>
        <p class="mt-1 text-sm text-slate-600">{{ $role->description }}</p>
        @if ($role->is_system)
            <p class="mt-2 text-xs text-slate-500">System roles cannot be deleted.</p>
        @endif
    </div>

    <form wire:submit="save" class="space-y-6 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        @foreach ($permissions as $group => $groupPermissions)
            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-500">{{ $group }}</h3>
                <div class="mt-3 grid gap-2 sm:grid-cols-2">
                    @foreach ($groupPermissions as $permission)
                        <label class="flex items-start gap-2 text-sm text-slate-700">
                            <input
                                type="checkbox"
                                wire:model="selectedPermissions"
                                value="{{ $permission->id }}"
                                class="mt-0.5 rounded border-slate-300 text-slate-900 focus:ring-slate-500"
                            >
                            <span>
                                <span class="font-medium">{{ $permission->name }}</span>
                                @if ($permission->description)
                                    <span class="block text-xs text-slate-500">{{ $permission->description }}</span>
                                @endif
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach

        @error('permissions')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror

        <div class="flex items-center justify-between">
            <a href="{{ route('admin.roles.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Cancel</a>
            <button
                type="submit"
                class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800"
            >
                Save permissions
            </button>
        </div>
    </form>
</div>
