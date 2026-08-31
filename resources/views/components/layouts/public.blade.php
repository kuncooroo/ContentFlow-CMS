@props(['title' => 'ContentFlow CMS', 'seo' => null])

@php($siteName = app(\App\Services\Settings\SiteSettings::class)->siteName())

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if ($siteSettings->faviconMedia ?? null)
        <link rel="icon" href="{{ $siteSettings->faviconMedia->url() }}">
    @endif

    @if ($seo)
        <x-seo-meta :metadata="$seo" />
    @else
        <title>{{ $title }} — {{ $siteName }}</title>
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-white text-slate-900 antialiased">
    <x-demo-banner />
    <header class="border-b border-slate-200 bg-slate-50">
        <div class="mx-auto flex max-w-5xl flex-wrap items-center justify-between gap-4 px-4 py-4 lg:px-6">
            <a href="{{ route('home') }}" class="flex items-center gap-3 text-lg font-semibold text-slate-900">
                @if ($siteSettings->logoMedia ?? null)
                    <img src="{{ $siteSettings->logoMedia->url() }}" alt="{{ $siteName }}" class="h-8 w-auto">
                @endif
                <span>{{ $siteName }}</span>
            </a>
            <nav class="flex flex-wrap items-center gap-4 text-sm text-slate-600">
                @foreach ($navItems as $item)
                    <a href="{{ $item['url'] }}" class="hover:text-slate-900">{{ $item['label'] }}</a>
                @endforeach
                <a href="{{ route('login') }}" class="hover:text-slate-900">Admin login</a>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-10 lg:px-6">
        <div class="mb-6">
            <x-flash-messages />
        </div>
        {{ $slot }}
    </main>

    <footer class="border-t border-slate-200 bg-slate-50">
        <div class="mx-auto max-w-5xl px-4 py-6 text-center text-sm text-slate-500 lg:px-6">
            &copy; {{ date('Y') }} {{ $siteName }}
        </div>
    </footer>

    @livewireScripts
</body>
</html>
