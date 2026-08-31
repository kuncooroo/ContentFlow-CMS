<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Install' }} — ContentFlow CMS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    <div class="mx-auto flex min-h-screen max-w-3xl flex-col px-4 py-10">
        <header class="mb-8 text-center">
            <p class="text-sm font-medium uppercase tracking-wider text-slate-500">ContentFlow CMS</p>
            <h1 class="mt-2 text-2xl font-bold text-slate-900">{{ $heading ?? 'Installation wizard' }}</h1>
            @isset($step)
                <p class="mt-2 text-sm text-slate-600">Step {{ $step }} of 6</p>
            @endisset
        </header>

        <main class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            {{ $slot }}
        </main>
    </div>
</body>
</html>
