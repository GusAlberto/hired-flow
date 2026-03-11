@props([
    'isOpen' => false,
    'company' => '',
    'position' => '',
    'city' => '',
    'location' => '',
    'isLocationRemote' => false,
    'appliedAt' => null,
    'jobUrl' => '',
    'personalScore' => null,
    'salaryOffered' => null,
    'salaryExpected' => null,
    'jobSummary' => '',
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
            <label class="mb-2 flex items-center justify-between rounded-xl border border-gray-200 bg-white px-4 py-3">
                <span class="text-sm font-medium text-gray-700">Remote location</span>
                <input type="checkbox" wire:model.live="isLocationRemote" class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
            </label>
            <input
                type="text"
                placeholder="{{ $isLocationRemote ? 'Remote' : 'Location' }}"
                wire:model.live="location"
                @disabled($isLocationRemote)
                class="w-full border rounded px-3 py-2 {{ $isLocationRemote ? 'bg-gray-100 text-gray-500' : '' }}"
            />
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

        <div>
            <input type="number" min="0" step="0.01" placeholder="Company budget salary (optional)" wire:model.live="salary_offered" class="w-full border rounded px-3 py-2" />
            @error('salary_offered') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <input type="number" min="0" step="0.01" placeholder="Expected salary (optional)" wire:model.live="salary_expected" class="w-full border rounded px-3 py-2" />
            @error('salary_expected') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <textarea placeholder="Short job description (optional)" wire:model.live="job_summary" rows="4" class="w-full border rounded px-3 py-2"></textarea>
        @error('job_summary') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded font-semibold">
        Add application
    </button>
</form>
@endif
