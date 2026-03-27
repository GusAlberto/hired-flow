@props([
    'showDuplicates' => false,
])

<div x-data="{ expanded: true }"
    class="group relative mb-8 overflow-hidden rounded-3xl border border-slate-300/80 bg-gradient-to-br from-slate-100 via-zinc-50 to-slate-200/90 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
    <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-slate-300/30 blur-2xl"></div>
    <div class="pointer-events-none absolute -bottom-14 left-6 h-32 w-32 rounded-full bg-zinc-300/25 blur-2xl"></div>

    <div class="relative mb-4 flex items-center justify-center">
        <h2 x-show="expanded" class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-600">Dashboard</h2>
        <h2 x-show="!expanded" class="text-2xl font-black uppercase tracking-wider text-slate-700">DASHboard</h2>

        <button type="button" @click="expanded = !expanded"
            class="absolute right-0 inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-300/80 bg-white/90 text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-800"
            :aria-expanded="expanded" aria-label="Toggle dashboard" title="Show or hide dashboard">
            <svg class="h-4 w-4 transition-transform duration-200" :class="expanded ? 'rotate-180' : ''"
                viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                    stroke-linejoin="round"></path>
            </svg>
        </button>
    </div>

    <div x-show="expanded" x-transition.opacity.duration.150ms>
        @if ($showDuplicates)
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                Showing only duplicated applications. A card is marked as duplicated when it has the same key information as another one.
            </div>
        @endif

        {{ $slot }}
    </div>
</div>
