<!DOCTYPE html>
<html lang="en" class="h-full bg-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full tinos-regular">
    <div class="min-h-full">
        <nav class="bg-white border-b-3 border-black">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center gap-8">
                        <a href="{{ route('dashboard') }}" class="text-black tinos-bold text-lg">Admin</a>
                        <div class="flex gap-6">
                            <a href="{{ route('dashboard') }}"
                               class="py-2 text-sm {{ request()->routeIs('dashboard') ? 'tinos-bold border-b-2 border-black' : 'text-gray-600 hover:text-black' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('posts.index') }}"
                               class="py-2 text-sm {{ request()->routeIs('posts.*') ? 'tinos-bold border-b-2 border-black' : 'text-gray-600 hover:text-black' }}">
                                Posts
                            </a>
                            <a href="{{ route('projects.index') }}"
                               class="py-2 text-sm {{ request()->routeIs('projects.*') ? 'tinos-bold border-b-2 border-black' : 'text-gray-600 hover:text-black' }}">
                                Projects
                            </a>
                            <a href="{{ route('tracking-links.index') }}"
                               class="py-2 text-sm {{ request()->routeIs('tracking-links.*') ? 'tinos-bold border-b-2 border-black' : 'text-gray-600 hover:text-black' }}">
                                Links
                            </a>
                            <a href="{{ route('redirect-links.index') }}"
                               class="py-2 text-sm {{ request()->routeIs('redirect-links.*') ? 'tinos-bold border-b-2 border-black' : 'text-gray-600 hover:text-black' }}">
                                Redirects
                            </a>
                            <a href="{{ route('plesk.index') }}"
                               class="py-2 text-sm {{ request()->routeIs('plesk.*') ? 'tinos-bold border-b-2 border-black' : 'text-gray-600 hover:text-black' }}">
                                Plesk
                            </a>
                            <a href="{{ route('images.index') }}"
                               class="py-2 text-sm {{ request()->routeIs('images.*') ? 'tinos-bold border-b-2 border-black' : 'text-gray-600 hover:text-black' }}">
                                Images
                            </a>
                            <a href="{{ route('settings.index') }}"
                               class="py-2 text-sm {{ request()->routeIs('settings.*') ? 'tinos-bold border-b-2 border-black' : 'text-gray-600 hover:text-black' }}">
                                Settings
                            </a>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-black text-sm tinos-regular">Logout</button>
                    </form>
                </div>
            </div>
        </nav>

        <header class="bg-white border-b border-gray-200">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <h1 class="text-3xl tinos-bold text-black">@yield('header')</h1>
            </div>
        </header>

        <main>
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                @if(session('success'))
                    <div class="mb-4 border-black border border-r-3 border-b-3 p-4">
                        <p class="text-sm text-black tinos-regular">{{ session('success') }}</p>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 border-black border border-r-3 border-b-3 p-4">
                        <p class="text-sm text-black tinos-regular">{{ session('error') }}</p>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @yield('scripts')
</body>
</html>
