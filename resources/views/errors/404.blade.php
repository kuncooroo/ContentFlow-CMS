<x-layouts.public title="Page not found">
    <div class="mx-auto max-w-lg space-y-4 text-center">
        <p class="text-sm font-medium uppercase tracking-wider text-slate-500">404</p>
        <h1 class="text-3xl font-bold text-slate-900">Page not found</h1>
        <p class="text-slate-600">
            The page you requested could not be found.
        </p>
        <a href="{{ route('home') }}" class="inline-block rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
            Back to home
        </a>
    </div>
</x-layouts.public>
