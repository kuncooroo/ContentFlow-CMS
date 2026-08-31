<x-layouts.install heading="Initial site settings" :step="5">
    <form method="POST" action="{{ route('install.settings.store') }}" class="space-y-4">
        @csrf

        <div>
            <label for="site_name" class="block text-sm font-medium text-slate-700">Site name</label>
            <input id="site_name" name="site_name" type="text" value="{{ $siteName }}" required class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
            @error('site_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="timezone" class="block text-sm font-medium text-slate-700">Timezone</label>
            <select id="timezone" name="timezone" required class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                @foreach ($timezones as $tz)
                    <option value="{{ $tz }}" @selected($tz === old('timezone', $timezone))>{{ $tz }}</option>
                @endforeach
            </select>
            @error('timezone')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="locale" class="block text-sm font-medium text-slate-700">Locale</label>
            <input id="locale" name="locale" type="text" value="{{ $locale }}" required class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
            @error('locale')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
            Complete installation
        </button>
    </form>
</x-layouts.install>
