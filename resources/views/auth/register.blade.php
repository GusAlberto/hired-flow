<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>Create Your Account - HiredFlow</title>

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

  <style data-purpose="typography">
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
    body {
      font-family: 'Inter', sans-serif;
    }
  </style>
</head>
<body class="bg-white min-h-screen flex flex-col">
<header class="w-full px-4 py-6 flex flex-col items-center border-b border-gray-100">
  <div class="w-full flex justify-start mb-4">
    <a class="text-gray-500 flex items-center text-sm" href="{{ url()->previous() }}">
      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
      </svg>
      Back
    </a>
  </div>

  <div class="text-center" data-purpose="logo">
    <h1 class="font-bold text-xl tracking-tighter uppercase leading-none">
      Hired<br/>
      <span class="flex items-center justify-center">
        Flow <span class="ml-1 border-2 border-black rounded-full w-4 h-4 flex items-center justify-center text-[10px]">Q</span>
      </span>
    </h1>
  </div>
</header>

<main class="flex-grow flex flex-col items-center px-6 pt-10 pb-12 w-full max-w-md mx-auto">
  <div class="text-center mb-8">
    <h2 class="text-3xl font-bold text-gray-900 mb-2">Create Your Account</h2>
    <p class="text-gray-500 text-sm">Set up your account to continue</p>
  </div>

  <div class="w-full bg-white border border-gray-200 rounded-2xl p-8 shadow-sm" data-purpose="signup-form-container">
    <div class="mb-6">
      <h3 class="text-xl font-bold text-gray-900">Create Your Account</h3>
      <p class="text-xs text-gray-500">Start tracking your applications in one place</p>
    </div>

    <div class="mb-6">
      <button class="w-full flex items-center justify-center gap-3 border border-gray-300 py-3 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed" data-purpose="google-signup-button" type="button" disabled>
        <svg height="18" viewBox="0 0 18 18" width="18" aria-hidden="true">
          <path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z" fill="#4285F4"></path>
          <path d="M9 18c2.43 0 4.467-.806 5.956-2.184l-2.908-2.258c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 009 18z" fill="#34A853"></path>
          <path d="M3.964 10.707c-.18-.54-.282-1.117-.282-1.707s.102-1.167.282-1.707V4.96H.957A8.996 8.996 0 000 9c0 1.452.348 2.827.957 4.041l3.007-2.334z" fill="#FBBC05"></path>
          <path d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0 5.482 0 2.443 2.017.957 4.961L3.964 7.294C4.672 5.167 6.656 3.58 9 3.58z" fill="#EA4335"></path>
        </svg>
        <span class="text-sm font-medium text-gray-700">Continue with Google (coming soon)</span>
      </button>
    </div>

    <div class="relative flex items-center justify-center mb-6">
      <div class="border-t border-gray-200 w-full"></div>
      <span class="bg-white px-3 text-xs text-gray-400 absolute">OR</span>
    </div>

    <form action="{{ route('register') }}" class="space-y-4" method="POST">
      @csrf

      <div data-purpose="form-group">
        <label class="block text-sm font-semibold text-gray-700 mb-1" for="name">Full Name</label>
        <input
          class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-black focus:border-black placeholder-gray-400 transition-all"
          id="name"
          name="name"
          type="text"
          value="{{ old('name') }}"
          placeholder="Your full name"
          required
          autofocus
          autocomplete="name"
        />
        @error('name')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>

      <div data-purpose="form-group">
        <label class="block text-sm font-semibold text-gray-700 mb-1" for="email">Email</label>
        <input
          class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-black focus:border-black placeholder-gray-400 transition-all"
          id="email"
          name="email"
          type="email"
          value="{{ old('email') }}"
          placeholder="name@gmail.com"
          required
          autocomplete="username"
        />
        @error('email')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>

      <div data-purpose="form-group">
        <label class="block text-sm font-semibold text-gray-700 mb-1" for="password">Password</label>
        <input
          class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-black focus:border-black placeholder-gray-400 transition-all"
          id="password"
          name="password"
          type="password"
          placeholder="At least 8 characters"
          required
          autocomplete="new-password"
        />
        @error('password')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>

      <div data-purpose="form-group">
        <label class="block text-sm font-semibold text-gray-700 mb-1" for="password_confirmation">Confirm Password</label>
        <input
          class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-black focus:border-black placeholder-gray-400 transition-all"
          id="password_confirmation"
          name="password_confirmation"
          type="password"
          placeholder="Re-enter your password"
          required
          autocomplete="new-password"
        />
        @error('password_confirmation')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>

      <button class="w-full bg-brand-dark text-white py-3.5 rounded-lg font-bold text-sm hover:opacity-90 transition-opacity mt-4" data-purpose="submit-button" type="submit">
        Create Account &amp; Continue
      </button>
    </form>

    <div class="mt-6 text-center">
      <p class="text-sm text-gray-500">
        Already have an account?
        <a class="text-gray-900 font-semibold hover:underline" href="{{ route('login') }}">Sign in</a>
      </p>
    </div>
  </div>
</main>

<footer class="py-8"></footer>
</body>
</html>