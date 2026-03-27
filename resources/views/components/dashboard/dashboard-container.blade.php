@props([])

<div x-data="{ expanded: true }"
    class="group relative mb-0 overflow-hidden rounded-3xl border border-slate-400/80 bg-gradient-to-br from-white via-slate-50 to-slate-200 p-5 shadow-xl shadow-slate-300/40 transition hover:shadow-2xl hover:shadow-slate-400/40">
    <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-slate-400/25 blur-2xl"></div>
    <div class="pointer-events-none absolute -bottom-14 left-6 h-32 w-32 rounded-full bg-slate-500/15 blur-2xl"></div>

    <div class="relative mb-4 flex h-9 items-center justify-end">
        <h2 x-show="!expanded" class="pointer-events-none absolute inset-x-0 text-center text-2xl font-black tracking-tight text-slate-900">
            Dashboard
        </h2>
        
        <button type="button" @click="expanded = !expanded"
            class="relative z-10 inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-400/80 bg-white text-slate-600 shadow-sm transition hover:bg-slate-100 hover:text-slate-800"
            :aria-expanded="expanded" aria-label="Toggle dashboard" title="Show or hide dashboard">
            <svg class="h-4 w-4 transition-transform duration-200" :class="expanded ? 'rotate-180' : ''"
                viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                    stroke-linejoin="round"></path>
            </svg>
        </button>
    </div>

    <div x-show="expanded" x-transition.opacity.duration.150ms>
        {{ $slot }}
    </div>
</div>
