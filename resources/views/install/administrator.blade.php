<x-layouts.install heading="Super Admin account" :step="4">
    <form method="POST" action="{{ route('install.administrator.store') }}" class="space-y-4">
        @csrf

        <p class="text-sm text-slate-600">Create the first Super Admin account. The password is not shown on the next step.</p>

        <div>
            <label for="name" class="block text-sm font-medium text-slate-700">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                <input id="password" name="password" type="password" required class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
            </div>
        </div>

        <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
            Continue
        </button>
    </form>
</x-layouts.install>
