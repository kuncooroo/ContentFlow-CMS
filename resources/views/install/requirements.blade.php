<x-layouts.install heading="System requirements" :step="1">
    <div class="space-y-6">
        <p class="text-sm text-slate-600">Verify that this server meets the minimum requirements before continuing.</p>

        <ul class="divide-y divide-slate-200 rounded-md border border-slate-200">
            @foreach ($checks as $check)
                <li class="flex items-start justify-between gap-4 px-4 py-3 text-sm">
                    <div>
                        <p class="font-medium text-slate-900">{{ $check['name'] }}</p>
                        <p class="mt-1 text-slate-600">{{ $check['message'] }}</p>
                    </div>
                    <span @class([
                        'shrink-0 rounded-full px-2 py-1 text-xs font-medium',
                        'bg-green-100 text-green-800' => $check['passed'],
                        'bg-red-100 text-red-800' => ! $check['passed'],
                    ])>
                        {{ $check['passed'] ? 'OK' : 'Failed' }}
                    </span>
                </li>
            @endforeach
        </ul>

        @error('requirements')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror

        <form method="POST" action="{{ route('install.requirements.store') }}">
            @csrf
            <button
                type="submit"
                @disabled(! $passed)
                @class([
                    'rounded-md px-4 py-2 text-sm font-medium text-white',
                    'bg-slate-900 hover:bg-slate-800' => $passed,
                    'cursor-not-allowed bg-slate-400' => ! $passed,
                ])
            >
                Continue
            </button>
        </form>
    </div>
</x-layouts.install>
