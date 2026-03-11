@props([
    'isOpen' => false,
    'interviewDate' => null,
    'interviewTime' => '',
    'interviewLocation' => '',
    'interviewIsRemote' => false,
    'interviewPlatform' => '',
    'interviewAddress' => '',
])

@if ($isOpen)
<div class="fixed inset-0 z-50 overflow-y-auto bg-black/40 px-4 py-4 sm:py-8" wire:key="interview-modal">
    <div class="flex min-h-full items-start justify-center sm:items-center">
        <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl max-h-[90vh] overflow-hidden">
            <div class="flex items-center justify-between gap-4 border-b border-gray-200 px-4 py-4 sm:px-6">
                <h2 class="text-xl font-semibold text-gray-900">Schedule interview</h2>
                <button type="button" wire:click="closeInterviewModal" class="rounded-lg px-3 py-1 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900">
                    Close
                </button>
            </div>

            <form wire:submit.prevent="saveInterviewMove" class="flex max-h-[calc(90vh-73px)] flex-col">
                <div class="space-y-4 overflow-y-auto px-4 py-4 sm:px-6">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <input type="date" wire:model.defer="interviewDate" class="w-full border rounded px-3 py-2" />
                            @error('interviewDate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <input type="time" wire:model.defer="interviewTime" class="w-full border rounded px-3 py-2" />
                            @error('interviewTime') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <input type="text" placeholder="Interview location (optional)" wire:model.defer="interviewLocation" class="w-full border rounded px-3 py-2" />
                        @error('interviewLocation') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <label class="flex items-center justify-between rounded-xl border border-gray-200 px-4 py-3">
                        <span class="text-sm font-medium text-gray-700">Remote interview</span>
                        <input type="checkbox" wire:model.live="interviewIsRemote" class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                    </label>

                    @if ($interviewIsRemote)
                        <div>
                            <input type="text" placeholder="Platform (optional)" wire:model.defer="interviewPlatform" class="w-full border rounded px-3 py-2" />
                            @error('interviewPlatform') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    @else
                        <div>
                            <input type="text" placeholder="Interview address (optional)" wire:model.defer="interviewAddress" class="w-full border rounded px-3 py-2" />
                            @error('interviewAddress') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    @endif
                </div>

                <div class="flex justify-end gap-3 border-t border-gray-200 bg-white px-4 py-4 sm:px-6">
                    <button type="button" wire:click="closeInterviewModal" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">
                        Cancel
                    </button>
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        Save interview
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
