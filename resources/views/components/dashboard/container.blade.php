@props([
    'showDuplicates' => false,
])

<div x-data="{ expanded: true }" class="mb-8 rounded-2xl border border-gray-300 bg-gray-300 p-4">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-600">Dashboard</h2>

        <button type="button" @click="expanded = !expanded"
            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:bg-gray-100 hover:text-gray-700"
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
