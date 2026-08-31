<x-layouts.install heading="Application configuration" :step="2">
    <form method="POST" action="{{ route('install.application.store') }}" class="space-y-4">
        @csrf

        <div>
            <label for="app_name" class="block text-sm font-medium text-slate-700">Application name</label>
            <input id="app_name" name="app_name" type="text" value="{{ $appName }}" required class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
            @error('app_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="app_url" class="block text-sm font-medium text-slate-700">Application URL</label>
            <input id="app_url" name="app_url" type="url" value="{{ $appUrl }}" required class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
            @error('app_url')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
            Continue
        </button>
    </form>
</x-layouts.install>
