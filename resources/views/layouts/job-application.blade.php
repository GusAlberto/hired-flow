<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Applications') | {{ config('app.name', 'HiredFlow') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    <div class="flex min-h-screen">
        <aside class="hidden w-72 shrink-0 border-r border-slate-200 bg-white p-5 lg:flex lg:flex-col">
            <div class="mb-8">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">HiredFlow</p>
                <p class="mt-1 text-2xl font-black tracking-tight text-slate-900">Applications</p>
            </div>

            @php($activeMenu = trim($__env->yieldContent('activeMenu')))

            <nav class="space-y-2">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center rounded-xl px-4 py-3 text-sm font-semibold transition {{ $activeMenu === 'dashboard' ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    Dashboard
                </a>
                <a href="{{ route('applications.create') }}"
                    class="flex items-center rounded-xl px-4 py-3 text-sm font-semibold transition {{ $activeMenu === 'applications.create' ? 'bg-blue-700 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
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

        <div class="flex min-h-screen flex-1 flex-col">
            <header class="border-b border-slate-200 bg-white px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-black tracking-tight text-slate-900">@yield('title', 'Applications')</h2>
                        <p class="text-xs text-slate-500">Keep your process clean and centralized.</p>
                    </div>
                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                        Back to dashboard
                    </a>
                </div>
            </header>

            <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                @if (session('status'))
                    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                        {{ session('status') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
