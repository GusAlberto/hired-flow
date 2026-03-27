<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'HiredFlow') }}</title>

        @livewireStyles
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-screen overflow-hidden bg-slate-100 font-sans antialiased text-slate-900">
        <div class="flex h-screen overflow-hidden">
            <aside class="hidden h-screen w-72 shrink-0 border-r border-slate-200 bg-white p-5 lg:flex lg:flex-col lg:sticky lg:top-0">
                <div class="mb-8">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">HiredFlow</p>
                    <p class="mt-1 text-2xl font-black tracking-tight text-slate-900">Applications</p>
                </div>

                <nav class="space-y-2">
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('board') }}"
                        class="flex items-center rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('board') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                        Board
                    </a>
                    <a href="{{ route('applications.create') }}"
                        class="flex items-center rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('applications.*') ? 'bg-[#0D1B2A] text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                        New application
                    </a>
                    <a href="{{ route('settings.index') }}"
                        class="flex items-center rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('settings.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
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
                @isset($header)
                    <header class="border-b border-slate-200 bg-white px-4 py-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </header>
                @endisset

                <main class="flex-1 overflow-y-auto px-4 py-6 sm:px-6 lg:px-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>

@livewireScripts
