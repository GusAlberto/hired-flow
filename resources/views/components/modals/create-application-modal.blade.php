@props([
    'isOpen' => false,
    'company' => '',
    'position' => '',
    'city' => '',
    'location' => '',
    'appliedAt' => null,
    'jobUrl' => '',
    'personalScore' => null,
])

@if ($isOpen)
<form wire:submit.prevent="saveApplication" class="bg-white shadow rounded p-4 mb-8 space-y-4">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">
                New application
            </h2>
            <p class="text-sm text-gray-500">
                Fill in the details shown in the board card.
            </p>
        </div>

        <button type="button" wire:click="closeCreateForm" class="text-sm font-medium text-gray-600 hover:text-gray-900">
            Close
        </button>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        <div>
            <input type="text" placeholder="Company name" wire:model.live="company" class="w-full border rounded px-3 py-2" />
            @error('company') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <input type="text" placeholder="Position" wire:model.live="position" class="w-full border rounded px-3 py-2" />
            @error('position') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <input type="text" placeholder="City" wire:model.live="city" class="w-full border rounded px-3 py-2" />
            @error('city') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <input type="text" placeholder="Location" wire:model.live="location" class="w-full border rounded px-3 py-2" />
            @error('location') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <input type="date" wire:model.live="applied_at" class="w-full border rounded px-3 py-2" />
            @error('applied_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <input type="url" placeholder="Job URL (optional)" wire:model.live="job_url" class="w-full border rounded px-3 py-2" />
            @error('job_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <input type="number" min="0" max="10" step="1" placeholder="Personal score (0-10)" wire:model.live="personal_score" class="w-full border rounded px-3 py-2" />
            @error('personal_score') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded font-semibold">
        Add application
    </button>
</form>
@endif
