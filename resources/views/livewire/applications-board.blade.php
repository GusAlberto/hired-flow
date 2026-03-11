<div class="p-8">
    @php
        $columns = [
            'applied' => ['label' => 'Applied', 'items' => $applied],
            'waiting' => ['label' => 'Waiting', 'items' => $waiting],
            'interview' => ['label' => 'Interview', 'items' => $interview],
            'rejected' => ['label' => 'Rejected', 'items' => $rejected],
            'offer' => ['label' => 'Offer', 'items' => $offer],
        ];
    @endphp

    @if (session('status'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 2200)" x-show="show" class="fixed right-6 top-6 z-50 rounded-2xl bg-gray-900 px-4 py-3 text-sm font-medium text-white shadow-xl">
            {{ session('status') }}
        </div>
    @endif

    <div class="mb-8 flex items-center justify-between gap-4">
        <h1 class="text-3xl font-bold">
            Job Application Tracker
        </h1>

        <button type="button" wire:click="openCreateForm" class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
            + New application
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">

        <div class="bg-white shadow p-4 rounded">

            Total applications

            <div class="text-2xl font-bold">
                {{ $total }}
            </div>

        </div>

        <div class="bg-white shadow p-4 rounded">

            Interviews

            <div class="text-2xl font-bold">
                {{ $interviews }}
            </div>

        </div>

        <div class="bg-white shadow p-4 rounded">

            Offers

            <div class="text-2xl font-bold">
                {{ $offers }}
            </div>

        </div>

        <button type="button" wire:click="toggleFavoritesFilter" class="bg-white shadow p-4 rounded text-left transition border {{ $showFavoritesOnly ? 'border-yellow-400 ring-2 ring-yellow-200' : 'border-transparent hover:border-yellow-300' }}">

            Favorite jobs

            <div class="text-2xl font-bold flex items-center gap-2">
                <span>{{ $favorites }}</span>
                <span class="text-yellow-500">★</span>
            </div>

            <div class="text-xs text-gray-500 mt-1">
                Click to {{ $showFavoritesOnly ? 'show all jobs' : 'filter only favorites' }}
            </div>

        </button>

    </div>

    @if ($showFavoritesOnly)
        <div class="mb-4 flex items-center justify-between rounded-xl border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-900">
            <span>Showing only favorite applications.</span>
            <button type="button" wire:click="clearFavoritesFilter" class="font-semibold underline">
                Clear filter
            </button>
        </div>
    @endif

    @if ($isCreateFormOpen)
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

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-5">
        @foreach ($columns as $status => $column)
        <div class="rounded-2xl bg-gray-50 p-4 border border-gray-200">
            <div class="mb-3 flex items-center justify-between gap-3">
                <h2 class="font-bold text-gray-900">{{ $column['label'] }}</h2>
                <span class="inline-flex min-w-8 items-center justify-center rounded-full bg-white px-2.5 py-1 text-sm font-semibold text-gray-700 border border-gray-200">
                    {{ $column['items']->count() }}
                </span>
            </div>

            <div id="{{ $status }}" class="space-y-3 min-h-24">
                @foreach ($column['items'] as $app)
                <article class="card rounded-2xl border border-gray-200 bg-white p-4 shadow-sm" data-id="{{ $app->id }}" wire:key="application-{{ $app->id }}">
                    <div class="mb-3 flex items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-3">
                                @if ($hasFavoriteColumn)
                                    <div class="flex h-10 w-10 min-h-10 min-w-10 max-h-10 max-w-10 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white">
                                        <button
                                            type="button"
                                            wire:click="toggleFavorite({{ $app->id }})"
                                            class="flex h-full w-full items-center justify-center text-2xl leading-none transition {{ $app->is_favorite ? 'text-yellow-500' : 'text-gray-300 hover:text-yellow-400' }}"
                                            title="Mark as favorite"
                                            aria-label="Toggle favorite"
                                        >
                                            ★
                                        </button>
                                    </div>
                                @endif

                                <div class="text-sm font-semibold text-blue-700 uppercase">
                                    {{ $app->position }}
                                </div>
                            </div>
                            <div class="text-xs text-gray-400 pt-2">
                                {{ $app->applied_at?->format('d/m/Y') }}
                            </div>
                        </div>

                        <details class="card-actions relative">
                            <summary class="cursor-pointer list-none rounded-xl border border-gray-200 px-2.5 py-1 text-lg leading-none text-gray-500 transition hover:bg-gray-100 hover:text-gray-700">
                                ⋯
                            </summary>

                            <div class="absolute right-0 z-10 mt-2 w-36 rounded-xl border border-gray-200 bg-white p-2 shadow-lg">
                                <button type="button" wire:click="editApplication({{ $app->id }})" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-100">
                                    Edit
                                </button>
                                <button type="button" wire:click="deleteApplication({{ $app->id }})" wire:confirm="Delete this application?" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50">
                                    Delete
                                </button>
                            </div>
                        </details>
                    </div>

                    <div class="space-y-2 text-sm text-gray-700">
                        <div>
                            <span class="font-medium">🏢 Company name:</span>
                            {{ $app->company }}
                        </div>
                        <div>
                            <span class="font-medium">📍 City:</span>
                            {{ $app->city ?: 'Not informed' }}
                        </div>
                        <div>
                            <span class="font-medium">🗺️ Location:</span>
                            {{ $app->location ?: 'Not informed' }}
                        </div>
                        <div>
                            <span class="font-medium">⭐ Personal score:</span>
                            {{ is_null($app->personal_score) ? 'Not rated' : $app->personal_score . '/10' }}
                        </div>
                        @if ($app->status === 'interview' && ($app->interview_date || $app->interview_time || $app->interview_location || $app->interview_platform || $app->interview_address))
                        <div>
                            <span class="font-medium">📅 Interview:</span>
                            {{ $app->interview_date?->format('d/m/Y') ?? 'Date not set' }}
                            @if ($app->interview_time)
                                at {{ $app->interview_time }}
                            @endif
                        </div>
                        @if ($app->interview_location)
                        <div>
                            <span class="font-medium">📍 Interview location:</span>
                            {{ $app->interview_location }}
                        </div>
                        @endif
                        <div>
                            <span class="font-medium">🧭 Format:</span>
                            {{ $app->interview_is_remote ? 'Remote' : 'In person' }}
                        </div>
                        @if ($app->interview_is_remote && $app->interview_platform)
                        <div>
                            <span class="font-medium">💻 Platform:</span>
                            {{ $app->interview_platform }}
                        </div>
                        @endif
                        @if (!$app->interview_is_remote && $app->interview_address)
                        <div>
                            <span class="font-medium">🏢 Address:</span>
                            {{ $app->interview_address }}
                        </div>
                        @endif
                        @endif
                        @if ($app->notes)
                        <div>
                            <span class="font-medium">📝 Notes:</span>
                            {{ $app->notes }}
                        </div>
                        @endif
                    </div>
                </article>
                @endforeach

                @if ($column['items']->isEmpty())
                <div class="rounded-2xl border border-dashed border-gray-300 bg-white/60 px-4 py-6 text-center text-sm text-gray-400">
                    No applications in this column yet.
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    @if ($isEditModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4" wire:key="edit-modal">
        <div class="w-full max-w-2xl rounded-2xl bg-white p-6 shadow-2xl">
            <div class="mb-4 flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold text-gray-900">Edit application</h2>
                <button type="button" wire:click="closeEditModal" class="rounded-lg px-3 py-1 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900">
                    Close
                </button>
            </div>

            <form wire:submit.prevent="updateApplication" class="space-y-4">
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

                <div class="flex justify-end gap-3">
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
    @endif

    @if ($isInterviewModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4" wire:key="interview-modal">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
            <div class="mb-4 flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold text-gray-900">Schedule interview</h2>
                <button type="button" wire:click="closeInterviewModal" class="rounded-lg px-3 py-1 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900">
                    Close
                </button>
            </div>

            <form wire:submit.prevent="saveInterviewMove" class="space-y-4">
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
                        <input type="text" placeholder="Interview address" wire:model.defer="interviewAddress" class="w-full border rounded px-3 py-2" />
                        @error('interviewAddress') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                @endif

                <div class="flex justify-end gap-3">
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
    @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
    function initSortables() {
        ['applied', 'waiting', 'interview', 'rejected', 'offer'].forEach(status => {
            const el = document.getElementById(status)
            if (!el) return

            // avoid creating multiple Sortable instances for the same element
            if (el._sortable) return

            el._sortable = new Sortable(el, {
                group: 'jobs',
                animation: 150,
                filter: 'details, summary, button',
                preventOnFilter: false,
                onEnd: function(evt) {
                    const id = evt.item.dataset.id
                    const newStatus = evt.to.id

                    if (evt.from.id !== 'interview' && newStatus === 'interview') {
                        const referenceNode = evt.from.children[evt.oldIndex] ?? null
                        evt.from.insertBefore(evt.item, referenceNode)

                        Livewire.dispatch('prepareInterviewMove', { id: id })
                        return
                    }

                    // Livewire v3/v4: dispatch with named payload { id, status }
                    Livewire.dispatch('moveApplication', { id: id, status: newStatus })
                }
            })
        })
    }

    // initialize asap
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSortables)
    } else {
        initSortables()
    }

    // re-initialize after Livewire navigations / loads
    document.addEventListener('livewire:load', initSortables)
    document.addEventListener('livewire:navigated', initSortables)
</script>