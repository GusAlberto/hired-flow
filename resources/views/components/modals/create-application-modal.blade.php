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
])

@if ($isOpen)
<div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4" wire:click="closeCreateForm">
    <form wire:submit.prevent="saveApplication" wire:click.stop
        class="flex max-h-[92vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 lg:px-7">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">New application</h2>
                    <p class="text-sm text-slate-500">Create a new job application with detailed tracking info.</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" wire:click="closeCreateForm"
                    class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                    Cancel
                </button>
                <button type="submit"
                    class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-800">
                    Save application
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
                                <input type="text" wire:model.live="company" placeholder="Google"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" />
                                @error('company') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Position</label>
                                <input type="text" wire:model.live="position" placeholder="Senior UI Designer"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" />
                                @error('position') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">City</label>
                                <input type="text" wire:model.live="city" placeholder="Mountain View"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" />
                                @error('city') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">Work model</label>
                                <div x-data="{ workModel: @entangle('location').live }" class="flex flex-wrap gap-2">
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
                                @error('location') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </section>

                    <section class="rounded-xl border border-slate-200 bg-white p-5">
                        <h3 class="mb-4 text-base font-bold text-slate-900">Application Details</h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Applied date</label>
                                <input type="date" wire:model.live="applied_at"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" />
                                @error('applied_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Job URL</label>
                                <input type="url" wire:model.live="job_url" placeholder="https://company.com/job"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" />
                                @error('job_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </section>
                </div>

                <div class="space-y-6">
                    <section class="rounded-xl border border-slate-200 bg-white p-5">
                        <h3 class="mb-4 text-base font-bold text-slate-900">Quick Actions</h3>
                        <div class="space-y-3">
                            <button type="button"
                                class="flex w-full cursor-not-allowed items-center justify-center rounded-xl bg-blue-700 px-4 py-3 text-sm font-bold text-white opacity-70">
                                Move to Next Stage
                            </button>
                            <button type="button"
                                class="flex w-full cursor-not-allowed items-center justify-center rounded-xl bg-slate-100 px-4 py-3 text-sm font-bold text-slate-500">
                                Archive Application
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">Available after the application is created.</p>
                    </section>

                    <section class="rounded-xl border border-slate-200 bg-white p-5">
                        <h3 class="mb-4 text-base font-bold text-slate-900">Quick Stats</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Personal score (0-10)</label>
                                <input type="number" min="0" max="10" step="0.1" inputmode="decimal" wire:model.live="personal_score" placeholder="8.5"
                                    oninput="if (this.value !== '') { const v = Number(this.value); this.value = Number.isFinite(v) ? Math.min(10, Math.max(0, v)) : ''; }"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" />
                                @error('personal_score') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Company budget salary</label>
                                <input type="number" min="0" step="0.01" wire:model.live="salary_offered" placeholder="15000"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" />
                                @error('salary_offered') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Expected salary</label>
                                <input type="number" min="0" step="0.01" wire:model.live="salary_expected" placeholder="18000"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" />
                                @error('salary_expected') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </section>

                    <section class="rounded-xl border border-slate-200 bg-white p-5">
                        <h4 class="mb-4 text-sm font-bold text-slate-900">Next Event</h4>
                        <div class="rounded-lg border-l-4 border-blue-600 bg-blue-50 p-3">
                            <p class="text-xs font-bold uppercase text-blue-700">No event scheduled yet</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900">Create first, then schedule interviews</p>
                            <p class="text-xs text-slate-500">Remote or in-person details can be added later.</p>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </form>
</div>
@endif
