<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Sign In - HiredFlow</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-dark': '#2D2D2D',
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body class="login-page bg-white min-h-screen flex flex-col">
<header class="login-header w-full px-6 py-2.5 flex flex-col items-center border-b border-gray-100">
    <div class="w-full flex justify-start mb-1.5">
        <a class="login-back text-gray-500 flex items-center text-xs" href="{{ url('/') }}">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
            </svg>
            Back
        </a>
    </div>

    <div class="text-center">
        <h1 class="login-brand font-bold tracking-tighter uppercase leading-none">
            Hired<br/>
            <span class="flex items-center justify-center">
                Flow <span class="login-brand-badge ml-1 border-2 border-black rounded-full w-3.5 h-3.5 flex items-center justify-center text-[9px]">Q</span>
            </span>
        </h1>
    </div>
</header>

<main class="login-main flex-grow flex flex-col items-center justify-center px-5 py-2 w-full max-w-lg mx-auto">
    <div class="login-heading text-center mb-2">
        <h2 class="text-xl font-bold text-gray-900 mb-0.5">Sign In</h2>
        <p class="text-gray-500 text-[11px]">Access your dashboard and keep tracking applications</p>
    </div>

    <div class="login-card w-full bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
        <x-auth-session-status class="mb-2 text-xs" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-2.5">
            @csrf

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1" for="email">Email</label>
                <input
                    id="email"
                    class="login-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-black focus:ring-2 focus:ring-black"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="name@gmail.com"
                />
                <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1" for="password">Password</label>
                <input
                    id="password"
                    class="login-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-black focus:ring-2 focus:ring-black"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="Your password"
                />
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
            </div>

            <div class="flex items-center justify-between pt-0.5">
                <label for="remember_me" class="inline-flex items-center text-xs text-gray-600">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-gray-800 shadow-sm focus:ring-gray-700" name="remember">
                    <span class="ms-2">Remember me</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-xs text-gray-600 hover:text-gray-900 underline" href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>

            <button class="login-submit w-full bg-brand-dark text-white py-2 rounded-lg font-bold text-xs hover:opacity-90 transition-opacity" type="submit">
                Sign In
            </button>
        </form>

        <div class="mt-2 text-center">
            <p class="text-[11px] text-gray-500">
                No account yet?
                <a class="text-gray-900 font-semibold hover:underline" href="{{ route('register') }}">Create one</a>
            </p>
        </div>
    </div>
</main>

<footer class="login-footer"></footer>
</body>
</html>
