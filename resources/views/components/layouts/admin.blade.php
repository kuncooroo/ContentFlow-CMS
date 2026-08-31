@props(['title' => 'Dashboard'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} — {{ config('app.name', 'ContentFlow CMS') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    <x-demo-banner />
    <div class="flex min-h-screen">
        <aside class="hidden w-64 shrink-0 border-r border-slate-200 bg-slate-900 text-slate-100 lg:block">
            <div class="border-b border-slate-700 px-5 py-4 text-sm font-semibold tracking-wide">
                {{ config('app.name', 'ContentFlow CMS') }}
            </div>
            <nav class="space-y-1 px-3 py-4 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="block rounded-md px-3 py-2 hover:bg-slate-800">
                    Dashboard
                </a>
                @can('viewAny', App\Models\User::class)
                    <a href="{{ route('admin.users.index') }}" class="block rounded-md px-3 py-2 hover:bg-slate-800">
                        Users
                    </a>
                @endcan
                @can('viewAny', App\Models\Role::class)
                    <a href="{{ route('admin.roles.index') }}" class="block rounded-md px-3 py-2 hover:bg-slate-800">
                        Roles
                    </a>
                @endcan
                @can('viewAny', App\Models\Category::class)
                    <a href="{{ route('admin.categories.index') }}" class="block rounded-md px-3 py-2 hover:bg-slate-800">
                        Categories
                    </a>
                @endcan
                @can('viewAny', App\Models\Tag::class)
                    <a href="{{ route('admin.tags.index') }}" class="block rounded-md px-3 py-2 hover:bg-slate-800">
                        Tags
                    </a>
                @endcan
                @can('viewAny', App\Models\Media::class)
                    <a href="{{ route('admin.media.index') }}" class="block rounded-md px-3 py-2 hover:bg-slate-800">
                        Media
                    </a>
                @endcan
                @can('viewAny', App\Models\Post::class)
                    <a href="{{ route('admin.posts.index') }}" class="block rounded-md px-3 py-2 hover:bg-slate-800">
                        Posts
                    </a>
                @endcan
                @can('viewAny', App\Models\Page::class)
                    <a href="{{ route('admin.pages.index') }}" class="block rounded-md px-3 py-2 hover:bg-slate-800">
                        Pages
                    </a>
                @endcan
                @can('viewAny', App\Models\Comment::class)
                    <a href="{{ route('admin.comments.index') }}" class="block rounded-md px-3 py-2 hover:bg-slate-800">
                        Comments
                    </a>
                @endcan
                @can('viewAny', App\Models\Menu::class)
                    <a href="{{ route('admin.menus.index') }}" class="block rounded-md px-3 py-2 hover:bg-slate-800">
                        Menus
                    </a>
                @endcan
                @can('viewAny', App\Models\SiteSetting::class)
                    <a href="{{ route('admin.settings.general') }}" class="block rounded-md px-3 py-2 hover:bg-slate-800">
                        Settings
                    </a>
                @endcan
                @can('viewAny', App\Models\ActivityLog::class)
                    <a href="{{ route('admin.audit.index') }}" class="block rounded-md px-3 py-2 hover:bg-slate-800">
                        Activity log
                    </a>
                @endcan
            </nav>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="flex items-center justify-between border-b border-slate-200 bg-white px-4 py-3 lg:px-6">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500">Administration</p>
                    <h1 class="text-lg font-semibold text-slate-900">{{ $title }}</h1>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-slate-600">{{ auth()->user()->name }}</span>
                    <a href="{{ route('home') }}" class="text-sm text-slate-600 hover:text-slate-900">View site</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-slate-600 hover:text-slate-900">
                            Sign out
                        </button>
                    </form>
                </div>
            </header>

            <main class="flex-1 px-4 py-6 lg:px-6">
                <div class="mb-6 space-y-4">
                    <x-flash-messages />
                </div>
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
