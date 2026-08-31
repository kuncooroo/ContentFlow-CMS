<div class="mx-auto max-w-2xl">
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
            <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
            <input
                id="password"
                type="password"
                wire:model="password"
                class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
            >
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirm password</label>
            <input
                id="password_confirmation"
                type="password"
                wire:model="password_confirmation"
                class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
            >
        </div>

        <div>
            <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
            <select
                id="status"
                wire:model="status"
                class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
            >
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            @error('status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
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
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('admin.users.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Cancel</a>
            <button
                type="submit"
                class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800"
            >
                Create user
            </button>
        </div>
    </form>
</div>
