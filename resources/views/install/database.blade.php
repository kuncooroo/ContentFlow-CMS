<x-layouts.install heading="Database configuration" :step="3">
    <form method="POST" action="{{ route('install.database.store') }}" class="space-y-4">
        @csrf

        <p class="text-sm text-slate-600">
            Database credentials are saved to <code class="rounded bg-slate-100 px-1">.env</code> and are never shown again on later steps.
        </p>

        @error('database')
            <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $message }}</div>
        @enderror

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="host" class="block text-sm font-medium text-slate-700">Host</label>
                <input id="host" name="host" type="text" value="{{ $host }}" required class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                @error('host')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="port" class="block text-sm font-medium text-slate-700">Port</label>
                <input id="port" name="port" type="number" value="{{ $port }}" required class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                @error('port')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="database" class="block text-sm font-medium text-slate-700">Database name</label>
            <input id="database" name="database" type="text" value="{{ $database }}" required class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
            @error('database')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="username" class="block text-sm font-medium text-slate-700">Username</label>
                <input id="username" name="username" type="text" value="{{ $username }}" required class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                @error('username')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                <input id="password" name="password" type="password" autocomplete="new-password" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
            Test connection, migrate, and continue
        </button>
    </form>
</x-layouts.install>
