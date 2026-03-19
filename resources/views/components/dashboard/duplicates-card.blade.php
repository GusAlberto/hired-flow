@props([
    'duplicateCount' => 0,
    'showDuplicates' => false,
])

<button type="button" wire:click="toggleDuplicatesFilter"
    class="rounded-xl bg-white p-5 text-left shadow transition border {{ $showDuplicates ? 'border-red-300 ring-2 ring-red-200' : 'border-transparent hover:border-red-200' }}">
    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Duplicates</div>
    <div class="mt-3 flex items-end gap-2">
        <span class="text-5xl font-black leading-none text-red-500">{{ $duplicateCount }}</span>
        <span class="text-lg leading-none text-red-500">⚠</span>
    </div>

    <div class="mt-2 text-xs text-gray-500">
        {{ $duplicateCount === 0 ? 'No duplicate groups found' : ($showDuplicates ? 'Showing only duplicated jobs' : 'Click to filter duplicated jobs') }}
    </div>
</button>
