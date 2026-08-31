<x-layouts.guest title="Forgot password">
    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-xl font-semibold text-slate-900">Forgot password</h1>
        <p class="mt-1 text-sm text-slate-600">
            Enter your email address and we will send you a password reset link if an active account exists.
        </p>

        <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
                >
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800"
            >
                Email reset link
            </button>
        </form>

        <p class="mt-4 text-center text-sm text-slate-600">
            <a href="{{ route('login') }}" class="hover:text-slate-900">Back to sign in</a>
        </p>
    </div>
</x-layouts.guest>
