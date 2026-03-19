@props([
    'showDuplicates' => false,
])

<div x-data="{ expanded: true }" class="mb-8 rounded-2xl border border-gray-300 bg-gray-300 p-4">
    <div class="relative mb-4 flex items-center justify-center">
        <h2 x-show="expanded" class="text-sm font-semibold uppercase tracking-wide text-gray-600">Dashboard</h2>
        <h2 x-show="!expanded" class="text-2xl font-black uppercase tracking-wider text-gray-700">DASHboard</h2>

        <button type="button" @click="expanded = !expanded"
            class="absolute right-0 inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-100 bg-white text-gray-600 shadow-sm transition hover:bg-gray-50 hover:text-gray-800"
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
