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

    <x-modals.create-application-modal 
        :isOpen="$isCreateFormOpen"
        :company="$company"
        :position="$position"
        :city="$city"
        :location="$location"
        :appliedAt="$applied_at"
        :jobUrl="$job_url"
        :personalScore="$personal_score"
        :salaryOffered="$salary_offered"
        :salaryExpected="$salary_expected"
        :jobSummary="$job_summary"
    />

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
                        <div>
                            <span class="font-medium">💼 Company budget:</span>
                            {{ is_null($app->salary_offered) ? 'Not informed' : 'R$ ' . number_format((float) $app->salary_offered, 2, ',', '.') }}
                        </div>
                        <div>
                            <span class="font-medium">🎯 Expected salary:</span>
                            {{ is_null($app->salary_expected) ? 'Not informed' : 'R$ ' . number_format((float) $app->salary_expected, 2, ',', '.') }}
                        </div>
                        @if ($app->job_summary)
                        <div>
                            <span class="font-medium">📌 Job summary:</span>
                            {{ $app->job_summary }}
                        </div>
                        @endif
                        @if ($app->status === 'interview' && ($app->interview_date || $app->interview_time || $app->interview_location || $app->interview_platform || $app->interview_address))
                        <div class="my-2 border-t border-gray-200"></div>
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

    <x-modals.edit-application-modal
        :isOpen="$isEditModalOpen"
        :company="$editCompany"
        :position="$editPosition"
        :city="$editCity"
        :location="$editLocation"
        :appliedAt="$editAppliedAt"
        :jobUrl="$editJobUrl"
        :personalScore="$editPersonalScore"
        :salaryOffered="$editSalaryOffered"
        :salaryExpected="$editSalaryExpected"
        :jobSummary="$editJobSummary"
        :notes="$editNotes"
        :editingIsInterview="$editingIsInterview"
        :interviewDate="$editInterviewDate"
        :interviewTime="$editInterviewTime"
        :interviewIsRemote="$editInterviewIsRemote"
        :interviewPlatform="$editInterviewPlatform"
        :interviewAddress="$editInterviewAddress"
    />

    <x-modals.interview-scheduling-modal
        :isOpen="$isInterviewModalOpen"
        :interviewDate="$interviewDate"
        :interviewTime="$interviewTime"
        :interviewIsRemote="$interviewIsRemote"
        :interviewPlatform="$interviewPlatform"
        :interviewAddress="$interviewAddress"
    />

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