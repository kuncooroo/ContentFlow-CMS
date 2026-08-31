<x-layouts.install heading="Installation complete" :step="6">
    <div class="space-y-6 text-center">
        <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            ContentFlow CMS is installed and the installer has been locked.
        </div>

        <p class="text-sm text-slate-600">
            Sign in with your Super Admin account to access the administration area.
        </p>

        <a href="{{ route('login') }}" class="inline-block rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
            Go to sign in
        </a>
    </div>
</x-layouts.install>
