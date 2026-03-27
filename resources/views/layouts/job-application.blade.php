<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php($pageTitle = $title ?? trim($__env->yieldContent('title')) ?: (request()->routeIs('board') ? 'Board' : 'Applications'))
    <title>{{ $pageTitle }} | {{ config('app.name', 'HiredFlow') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-screen overflow-hidden bg-slate-100 text-slate-900 antialiased">
    <div class="flex h-screen overflow-hidden">
        <aside class="hidden h-screen w-56 shrink-0 border-r border-slate-200 bg-white p-5 lg:flex lg:flex-col lg:sticky lg:top-0">
            <div class="mb-8">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">HiredFlow</p>
                <p class="mt-1 text-2xl font-black tracking-tight text-slate-900">Applications</p>
            </div>

            @php($activeMenu = $activeMenu ?? trim($__env->yieldContent('activeMenu')) ?: request()->route()?->getName() ?? '')

            <nav class="space-y-2">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center rounded-xl px-4 py-3 text-sm font-semibold transition {{ $activeMenu === 'dashboard' ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    Dashboard
                </a>
                <a href="{{ route('board') }}"
                    class="flex items-center rounded-xl px-4 py-3 text-sm font-semibold transition {{ $activeMenu === 'board' ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    Board
                </a>
                <a href="{{ route('applications.create') }}"
                    class="flex items-center rounded-xl px-4 py-3 text-sm font-semibold transition {{ $activeMenu === 'applications.create' ? 'bg-[#0D1B2A] text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    New application
                </a>
                <a href="{{ route('settings.index') }}"
                    class="flex items-center rounded-xl px-4 py-3 text-sm font-semibold transition {{ str_starts_with($activeMenu, 'settings') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    Settings
                </a>
            </nav>

            <div class="mt-auto space-y-2 pt-8">
                <a href="{{ route('profile.edit') }}"
                    class="flex items-center rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                    Profile
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                        Sign out
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex h-screen flex-1 flex-col overflow-hidden">
            <header class="border-b border-slate-200 bg-white px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        @if (isset($title) && trim((string) $title) !== '')
                            <h2 class="text-lg font-black tracking-tight text-slate-900">{{ $title }}</h2>
                        @else
                            <h2 class="text-lg font-black tracking-tight text-slate-900">@yield('title', 'Applications')</h2>
                        @endif
                        <p class="text-xs text-slate-500">Keep your process clean and centralized.</p>
                    </div>
                    @if (in_array($activeMenu, ['board', 'dashboard'], true))
                        <a href="{{ route('applications.create') }}"
                            class="inline-flex items-center gap-2 whitespace-nowrap rounded-full bg-[#0D1B2A] px-4 py-2.5 text-sm font-semibold text-white shadow-md transition hover:bg-[#415A77] hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-[#415A77] focus:ring-offset-1 active:scale-[0.99]">
                            <span
                                class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-white/25 text-xs font-bold">+</span>
                            Create application
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                            Back to dashboard
                        </a>
                    @endif
                </div>
            </header>

            <main class="flex-1 overflow-y-auto px-6 py-2 sm:px-6 lg:px-8">
                @if (session('status'))
                    <div
                        class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                        {{ session('status') }}
                    </div>
                @endif

                @if (isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif

                <x-site-footer />
            </main>
        </div>
    </div>
</body>

</html>
