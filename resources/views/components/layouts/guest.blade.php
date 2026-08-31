@props(['title' => 'Sign in'])

@php($siteName = app(\App\Services\Settings\SiteSettings::class)->siteName())

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} — {{ $siteName }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-12">
        <a href="{{ route('home') }}" class="mb-8 text-lg font-semibold text-slate-900">
            {{ $siteName }}
        </a>

        <div class="w-full max-w-md space-y-4">
            <x-flash-messages />

            {{ $slot }}
        </div>

        <p class="mt-8 text-center text-sm text-slate-500">
            <a href="{{ route('home') }}" class="hover:text-slate-700">Back to website</a>
        </p>
    </div>
</body>
</html>
