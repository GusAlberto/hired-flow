@props([
    'isOpen' => false,
    'company' => '',
    'position' => '',
    'city' => '',
    'location' => '',
    'appliedAt' => null,
    'jobUrl' => '',
    'personalScore' => null,
    'salaryOffered' => null,
    'salaryExpected' => null,
    'notes' => '',
    'editingIsInterview' => false,
    'interviewDate' => null,
    'interviewTime' => '',
    'interviewIsRemote' => false,
    'interviewPlatform' => '',
    'interviewAddress' => '',
    'currentStatus' => null,
])

@if ($isOpen)
<div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4" wire:key="edit-modal" wire:click="closeEditModal">
    <form wire:submit.prevent="updateApplication" wire:click.stop
        class="flex max-h-[92vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 lg:px-7">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M4 12H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        <path d="M12 4L20 12L12 20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Application details</h2>
                    <p class="text-sm text-slate-500">Review and update this application using the same workflow panel.</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" wire:click="closeEditModal"
                    class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                    Cancel
                </button>
                <button type="submit"
                    class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-800">
                    Save changes
                </button>
            </div>
        </div>

        <div class="overflow-y-auto bg-slate-50 px-5 py-5 lg:px-7">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <section class="rounded-xl border border-slate-200 bg-white p-5">
                        <h3 class="mb-4 text-base font-bold text-slate-900">Role Information</h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Company name</label>
                                <input type="text" placeholder="Company name" wire:model.defer="editCompany"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" />
                                @error('editCompany') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Position</label>
                                <input type="text" placeholder="Position" wire:model.defer="editPosition"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" />
                                @error('editPosition') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">City</label>
                                <input type="text" placeholder="City" wire:model.defer="editCity"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" />
                                @error('editCity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">Work model</label>
                                <div x-data="{ workModel: @entangle('editLocation').live }" class="flex flex-wrap gap-2">
                                    <button type="button" @click="workModel = 'In person'"
                                        class="inline-flex items-center rounded-full border px-3.5 py-2 text-sm font-semibold transition-all duration-150 hover:-translate-y-0.5 hover:shadow-sm"
                                        :class="workModel === 'In person' ? 'border-blue-300 bg-blue-50 text-blue-700 ring-2 ring-blue-100' : 'border-slate-300 bg-white text-slate-700 hover:border-slate-400'">
                                        In person
                                    </button>

                                    <button type="button" @click="workModel = 'Remote'"
                                        class="inline-flex items-center rounded-full border px-3.5 py-2 text-sm font-semibold transition-all duration-150 hover:-translate-y-0.5 hover:shadow-sm"
                                        :class="workModel === 'Remote' ? 'border-blue-300 bg-blue-50 text-blue-700 ring-2 ring-blue-100' : 'border-slate-300 bg-white text-slate-700 hover:border-slate-400'">
                                        Remote
                                    </button>

                                    <button type="button" @click="workModel = 'Hybrid'"
                                        class="inline-flex items-center rounded-full border px-3.5 py-2 text-sm font-semibold transition-all duration-150 hover:-translate-y-0.5 hover:shadow-sm"
                                        :class="workModel === 'Hybrid' ? 'border-blue-300 bg-blue-50 text-blue-700 ring-2 ring-blue-100' : 'border-slate-300 bg-white text-slate-700 hover:border-slate-400'">
                                        Hybrid
                                    </button>
                                </div>
                                @error('editLocation') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </section>

                    <section class="rounded-xl border border-slate-200 bg-white p-5">
                        <h3 class="mb-4 text-base font-bold text-slate-900">Application Details</h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Applied date</label>
                                <input type="date" wire:model.defer="editAppliedAt"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" />
                                @error('editAppliedAt') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Job URL</label>
                                <input type="url" placeholder="Job URL (optional)" wire:model.defer="editJobUrl"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" />
                                @error('editJobUrl') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Personal score (0-10)</label>
                                <input type="number" min="0" max="10" step="0.1" inputmode="decimal" placeholder="8.5" wire:model.defer="editPersonalScore"
                                    oninput="if (this.value !== '') { const v = Number(this.value); this.value = Number.isFinite(v) ? Math.min(10, Math.max(0, v)) : ''; }"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" />
                                @error('editPersonalScore') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Company budget salary</label>
                                <input type="number" min="0" step="0.01" placeholder="Company budget salary (optional)" wire:model.defer="editSalaryOffered"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" />
                                @error('editSalaryOffered') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Expected salary</label>
                                <input type="number" min="0" step="0.01" placeholder="Expected salary (optional)" wire:model.defer="editSalaryExpected"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" />
                                @error('editSalaryExpected') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </section>

                    <section class="rounded-xl border border-slate-200 bg-white p-5">
                        <h3 class="mb-4 text-base font-bold text-slate-900">Internal Notes</h3>
                        <textarea placeholder="Notes" wire:model.defer="editNotes" rows="4"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"></textarea>
                        @error('editNotes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </section>

                    @if ($editingIsInterview)
                    <section class="space-y-4 rounded-xl border border-slate-200 bg-white p-5">
                        <h3 class="text-base font-bold text-slate-900">Interview details</h3>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Date</label>
                                <input type="date" wire:model.defer="editInterviewDate"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" />
                                @error('editInterviewDate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Time</label>
                                <input type="time" wire:model.defer="editInterviewTime"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" />
                                @error('editInterviewTime') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <label class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <span class="text-sm font-medium text-slate-700">Remote interview</span>
                            <input type="checkbox" wire:model.live="editInterviewIsRemote" class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                        </label>

                        @if ($interviewIsRemote)
                            <div>
                                <input type="text" placeholder="Platform (optional)" wire:model.defer="editInterviewPlatform"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" />
                                @error('editInterviewPlatform') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        @else
                            <div>
                                <input type="text" placeholder="Interview address (optional)" wire:model.defer="editInterviewAddress"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" />
                                @error('editInterviewAddress') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        @endif
                    </section>
                    @endif
                </div>

                <div class="space-y-6">
                    <section class="rounded-xl border border-slate-200 bg-white p-5">
                        @php
                            $canMovePrevious = in_array($currentStatus, ['waiting', 'interview', 'offer'], true);
                        @endphp
                        <h3 class="mb-4 text-base font-bold text-slate-900">Quick Actions</h3>
                        <div class="space-y-3">
                            <div class="grid grid-cols-1 gap-3 {{ $canMovePrevious ? 'sm:grid-cols-2' : '' }}">
                                @if ($canMovePrevious)
                                    <button type="button" wire:click="moveEditingApplicationToPreviousStage"
                                        class="flex w-full items-center justify-center rounded-xl bg-slate-100 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
                                        Move to Previous Stage
                                    </button>
                                @endif

                                <button type="button" wire:click="moveEditingApplicationToNextStage"
                                    class="flex w-full items-center justify-center rounded-xl bg-blue-700 px-4 py-3 text-sm font-bold text-white transition hover:bg-blue-800">
                                    Move to Next Stage
                                </button>
                            </div>

                            <button type="button" wire:click="archiveEditingApplication"
                                class="flex w-full items-center justify-center rounded-xl bg-slate-100 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
                                Archive Application
                            </button>
                        </div>
                    </section>

                    <section class="rounded-xl border border-slate-200 bg-white p-5">
                        <h4 class="mb-4 text-sm font-bold text-slate-900">Next Event</h4>
                        <div class="rounded-lg border-l-4 border-blue-600 bg-blue-50 p-3">
                            <p class="text-xs font-bold uppercase text-blue-700">{{ $editingIsInterview ? 'Interview' : 'No event scheduled' }}</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900">
                                {{ $interviewDate ? \Carbon\Carbon::parse($interviewDate)->format('M d') : 'Set interview details' }}
                                {{ $interviewTime ? ', ' . $interviewTime : '' }}
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ $interviewIsRemote ? ($interviewPlatform ?: 'Remote') : ($interviewAddress ?: 'In person') }}
                            </p>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </form>
</div>
@endif
