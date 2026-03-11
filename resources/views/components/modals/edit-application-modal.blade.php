@props([
    'isOpen' => false,
    'company' => '',
    'position' => '',
    'city' => '',
    'location' => '',
    'appliedAt' => null,
    'jobUrl' => '',
    'personalScore' => null,
    'notes' => '',
    'editingIsInterview' => false,
    'interviewDate' => null,
    'interviewTime' => '',
    'interviewLocation' => '',
    'interviewIsRemote' => false,
    'interviewPlatform' => '',
    'interviewAddress' => '',
])

@if ($isOpen)
<div class="fixed inset-0 z-50 overflow-y-auto bg-black/40 px-4 py-4 sm:py-8" wire:key="edit-modal">
    <div class="flex min-h-full items-start justify-center sm:items-center">
        <div class="w-full max-w-lg sm:max-w-2xl rounded-2xl bg-white shadow-2xl max-h-[90vh] overflow-hidden">
            <div class="flex items-center justify-between gap-4 border-b border-gray-200 px-4 py-4 sm:px-6">
                <h2 class="text-xl font-semibold text-gray-900">Edit application</h2>
                <button type="button" wire:click="closeEditModal" class="rounded-lg px-3 py-1 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900">
                    Close
                </button>
            </div>

            <form wire:submit.prevent="updateApplication" class="flex max-h-[calc(90vh-73px)] flex-col">
                <div class="space-y-4 overflow-y-auto px-4 py-4 sm:px-6">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <input type="text" placeholder="Company name" wire:model.defer="editCompany" class="w-full border rounded px-3 py-2" />
                            @error('editCompany') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <input type="text" placeholder="Position" wire:model.defer="editPosition" class="w-full border rounded px-3 py-2" />
                            @error('editPosition') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <input type="text" placeholder="City" wire:model.defer="editCity" class="w-full border rounded px-3 py-2" />
                            @error('editCity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <input type="text" placeholder="Location" wire:model.defer="editLocation" class="w-full border rounded px-3 py-2" />
                            @error('editLocation') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <input type="date" wire:model.defer="editAppliedAt" class="w-full border rounded px-3 py-2" />
                            @error('editAppliedAt') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <input type="url" placeholder="Job URL (optional)" wire:model.defer="editJobUrl" class="w-full border rounded px-3 py-2" />
                            @error('editJobUrl') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <input type="number" min="0" max="10" step="1" placeholder="Personal score (0-10)" wire:model.defer="editPersonalScore" class="w-full border rounded px-3 py-2" />
                            @error('editPersonalScore') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <textarea placeholder="Notes" wire:model.defer="editNotes" rows="4" class="w-full border rounded px-3 py-2"></textarea>
                        @error('editNotes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    @if ($editingIsInterview)
                    <div class="space-y-4 rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="text-sm font-semibold text-gray-900">Interview details</div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <input type="date" wire:model.defer="editInterviewDate" class="w-full border rounded px-3 py-2" />
                                @error('editInterviewDate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <input type="time" wire:model.defer="editInterviewTime" class="w-full border rounded px-3 py-2" />
                                @error('editInterviewTime') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <input type="text" placeholder="Interview location (optional)" wire:model.defer="editInterviewLocation" class="w-full border rounded px-3 py-2" />
                            @error('editInterviewLocation') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <label class="flex items-center justify-between rounded-xl border border-gray-200 bg-white px-4 py-3">
                            <span class="text-sm font-medium text-gray-700">Remote interview</span>
                            <input type="checkbox" wire:model.live="editInterviewIsRemote" class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                        </label>

                        @if ($interviewIsRemote)
                            <div>
                                <input type="text" placeholder="Platform (optional)" wire:model.defer="editInterviewPlatform" class="w-full border rounded px-3 py-2" />
                                @error('editInterviewPlatform') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        @else
                            <div>
                                <input type="text" placeholder="Interview address (optional)" wire:model.defer="editInterviewAddress" class="w-full border rounded px-3 py-2" />
                                @error('editInterviewAddress') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        @endif
                    </div>
                    @endif
                </div>

                <div class="flex justify-end gap-3 border-t border-gray-200 bg-white px-4 py-4 sm:px-6">
                    <button type="button" wire:click="closeEditModal" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">
                        Cancel
                    </button>
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        Save changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
