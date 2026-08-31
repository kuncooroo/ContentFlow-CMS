<div class="mx-auto max-w-2xl space-y-6">
    <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-sm text-slate-600">
            Status:
            @if ($user->status->isActive())
                <span class="font-medium text-green-700">Active</span>
            @else
                <span class="font-medium text-slate-700">Inactive</span>
            @endif
        </p>
        <p class="mt-1 text-xs text-slate-500">Use the users list to activate or deactivate accounts.</p>
    </div>

    <form wire:submit="save" class="space-y-6 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700">Name</label>
            <input
                id="name"
                type="text"
                wire:model="name"
                class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
            >
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
            <input
                id="email"
                type="email"
                wire:model="email"
                class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
            >
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-slate-700">New password</label>
            <input
                id="password"
                type="password"
                wire:model="password"
                class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
            >
            <p class="mt-1 text-xs text-slate-500">Leave blank to keep the current password.</p>
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirm new password</label>
            <input
                id="password_confirmation"
                type="password"
                wire:model="password_confirmation"
                class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
            >
        </div>

        <div>
            <p class="block text-sm font-medium text-slate-700">Roles</p>
            <div class="mt-2 space-y-2">
                @foreach ($roles as $role)
                    <label class="flex items-center gap-2 text-sm text-slate-700">
                        <input
                            type="checkbox"
                            wire:model="selectedRoles"
                            value="{{ $role->id }}"
                            class="rounded border-slate-300 text-slate-900 focus:ring-slate-500"
                        >
                        {{ $role->name }}
                    </label>
                @endforeach
            </div>
            @error('selectedRoles')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('roles')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('admin.users.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Cancel</a>
            <button
                type="submit"
                class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800"
            >
                Save changes
            </button>
        </div>
    </form>
</div>
