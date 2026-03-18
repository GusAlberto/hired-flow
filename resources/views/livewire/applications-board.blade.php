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

    <div class="mb-6 flex flex-col gap-4">
        <!-- Search and Filters Row -->
        <div class="flex items-center gap-3 flex-wrap">
            <!-- Search Input -->
            <div class="relative flex-1 min-w-48">
                <input
                    type="text"
                    wire:model.live="searchQuery"
                    placeholder="Search companies, positions, notes..."
                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-700 placeholder-gray-500 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                />
                @if ($isSearching)
                    <button
                        type="button"
                        wire:click="clearSearch"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition"
                        aria-label="Clear search"
                    >
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                @endif
            </div>

            <!-- Status Filters -->
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-semibold text-gray-600 uppercase">Filter by:</span>
                
                <button
                    type="button"
                    wire:click="toggleStatusFilter('applied')"
                    class="rounded-lg px-3 py-1.5 text-xs font-semibold transition {{ in_array('applied', $statusFilters) ? 'bg-blue-100 text-blue-700 ring-2 ring-blue-300' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                >
                    Applied
                </button>
                
                <button
                    type="button"
                    wire:click="toggleStatusFilter('waiting')"
                    class="rounded-lg px-3 py-1.5 text-xs font-semibold transition {{ in_array('waiting', $statusFilters) ? 'bg-purple-100 text-purple-700 ring-2 ring-purple-300' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                >
                    Waiting
                </button>
                
                <button
                    type="button"
                    wire:click="toggleStatusFilter('interview')"
                    class="rounded-lg px-3 py-1.5 text-xs font-semibold transition {{ in_array('interview', $statusFilters) ? 'bg-amber-100 text-amber-700 ring-2 ring-amber-300' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                >
                    Interview
                </button>
                
                <button
                    type="button"
                    wire:click="toggleStatusFilter('rejected')"
                    class="rounded-lg px-3 py-1.5 text-xs font-semibold transition {{ in_array('rejected', $statusFilters) ? 'bg-red-100 text-red-700 ring-2 ring-red-300' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                >
                    Rejected
                </button>
                
                <button
                    type="button"
                    wire:click="toggleStatusFilter('offer')"
                    class="rounded-lg px-3 py-1.5 text-xs font-semibold transition {{ in_array('offer', $statusFilters) ? 'bg-emerald-100 text-emerald-700 ring-2 ring-emerald-300' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                >
                    Offer
                </button>

                @if (count($statusFilters) !== 5)
                    <button
                        type="button"
                        wire:click="resetStatusFilters"
                        class="rounded-lg px-3 py-1.5 text-xs font-semibold bg-gray-200 text-gray-700 hover:bg-gray-300 transition"
                    >
                        Reset
                    </button>
                @endif
            </div>
        </div>

        <!-- Duplicates Alert -->
        @if ($duplicateCount > 0)
            <div class="flex items-center gap-3 rounded-xl border border-orange-200 bg-orange-50 px-4 py-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-orange-200">
                    <svg class="h-5 w-5 text-orange-700" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-orange-900">
                        Found <span class="font-bold">{{ $duplicateCount }}</span> duplicate application{{ $duplicateCount !== 1 ? 's' : '' }}
                    </p>
                    <p class="text-xs text-orange-700 mt-0.5">
                        Same company and position detected. Review and remove duplicates if needed.
                    </p>
                </div>
            </div>
        @endif
    </div>

    <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-5">

        <div class="rounded-xl bg-white p-5 shadow">
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total applications</div>
            <div class="mt-3 text-5xl font-black leading-none text-blue-700">{{ $total }}</div>
        </div>

        <div class="rounded-xl bg-white p-5 shadow">
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Interviews</div>
            <div class="mt-3 text-5xl font-black leading-none text-amber-600">{{ $interviews }}</div>
        </div>

        <div class="rounded-xl bg-white p-5 shadow">
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Offers</div>
            <div class="mt-3 text-5xl font-black leading-none text-emerald-600">{{ $offers }}</div>
        </div>

        <button type="button" wire:click="toggleFavoritesFilter" class="rounded-xl bg-white p-5 text-left shadow transition border {{ $showFavoritesOnly ? 'border-yellow-400 ring-2 ring-yellow-200' : 'border-transparent hover:border-yellow-300' }}">
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Favorite jobs</div>
            <div class="mt-3 flex items-end gap-2">
                <span class="text-5xl font-black leading-none text-yellow-500">{{ $favorites }}</span>
                <span class="text-lg leading-none text-yellow-500">★</span>
            </div>

            <div class="mt-2 text-xs text-gray-500">
                Click to {{ $showFavoritesOnly ? 'show all jobs' : 'filter only favorites' }}
            </div>
        </button>

        <button type="button" wire:click="toggleArchivedSection" class="rounded-xl bg-white p-5 text-left shadow transition border {{ $showArchivedSection ? 'border-gray-500 ring-2 ring-gray-200' : 'border-transparent hover:border-gray-300' }}">
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Archived</div>
            <div class="mt-3 flex items-end gap-2">
                <span class="text-5xl font-black leading-none text-slate-600">{{ $archivedCount }}</span>
                <span class="text-lg leading-none text-slate-500">🗂️</span>
            </div>

            <div class="mt-2 text-xs text-gray-500">
                Click to {{ $showArchivedSection ? 'hide archived list' : 'show archived list' }}
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

    @if ($showArchivedSection)
        <div class="mb-8 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Archived applications</h2>
                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                    {{ $archivedCount }} items
                </span>
            </div>

            @if ($archived->isEmpty())
                <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-center text-sm text-gray-500">
                    No archived applications yet.
                </div>
            @else
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($archived as $app)
                        <article class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3" wire:key="archived-{{ $app->id }}">
                            <div class="text-sm font-semibold text-gray-900 uppercase">{{ $app->position }}</div>
                            <div class="text-sm text-gray-700">{{ $app->company }}</div>
                            <div class="mt-1 text-xs text-gray-500">Applied: {{ $app->applied_at?->format('d/m/Y') ?? '-' }}</div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    @include('livewire.search-results', [
        'searchResults' => $searchResults,
        'searchQuery' => $searchQuery,
        'isSearching' => $isSearching,
    ])

    <div class="my-8 flex items-center" aria-hidden="true">
        <div class="h-px w-full bg-gray-300"></div>
    </div>

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
    />

    <div class="mb-4 flex justify-end">
        <button
            type="button"
            wire:click="toggleKanbanOrientation"
            wire:loading.attr="disabled"
            wire:loading.class="cursor-not-allowed opacity-60"
            wire:target="toggleKanbanOrientation"
            aria-label="Toggle kanban orientation"
            class="group relative inline-flex h-14 w-14 items-center justify-center rounded-xl border border-gray-300 bg-white text-gray-700 shadow-sm transition hover:bg-gray-100"
        >
            <svg
                class="h-5 w-5 transition-transform duration-300 ease-out"
                style="transform: rotate({{ $kanbanOrientation === 'vertical' ? 90 : 0 }}deg)"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true"
            >
                <path d="M8 7H19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M16 4L19 7L16 10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M16 17H5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M8 14L5 17L8 20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
            </svg>

            <span class="pointer-events-none absolute right-0 top-full z-20 mt-2 whitespace-nowrap rounded-lg bg-gray-900 px-2.5 py-1.5 text-xs font-medium text-white opacity-0 transition-opacity duration-150 group-hover:opacity-100">
                {{ $kanbanOrientation === 'horizontal' ? 'Switch to vertical view' : 'Switch to horizontal view' }}
            </span>
        </button>
    </div>

    <div class="grid grid-cols-1 gap-6 {{ $kanbanOrientation === 'horizontal' ? 'xl:grid-cols-5' : '' }}">
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
                <article class="card rounded-2xl border border-gray-200 bg-white p-4 shadow-sm" data-id="{{ $app->id }}" wire:key="application-{{ $app->id }}" x-data="{ expanded: false }">
                    <div class="mb-3 flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
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
                            @php
                                $isInterviewToday = $app->interview_date?->isToday();
                                $isInterviewTomorrow = $app->interview_date?->isTomorrow();
                            @endphp
                            <div class="mt-2 text-xs text-gray-400">
                                {{ $app->applied_at?->format('d/m/Y') }}
                            </div>
                        </div>

                        <div class="flex shrink-0 flex-col items-end gap-2">
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

                            @if ($app->interview_date && ($isInterviewToday || $isInterviewTomorrow))
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $isInterviewToday ? 'bg-red-100 text-red-700 ring-1 ring-red-200' : 'bg-orange-100 text-orange-700 ring-1 ring-orange-200' }}">
                                    {{ $isInterviewToday ? 'Today' : 'Tomorrow' }}
                                </span>
                            @endif
                        </div>
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
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <span class="font-medium">⭐ Personal score:</span>
                                {{ is_null($app->personal_score) ? 'Not rated' : $app->personal_score . '/10' }}
                            </div>
                            <button
                                type="button"
                                @click="expanded = !expanded"
                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:bg-gray-100 hover:text-gray-700"
                                :aria-expanded="expanded"
                                aria-label="Toggle job details"
                                title="Show more details"
                            >
                                <svg
                                    class="h-4 w-4 transition-transform duration-200"
                                    :class="expanded ? 'rotate-180' : ''"
                                    viewBox="0 0 20 20"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                    aria-hidden="true"
                                >
                                    <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>

                        <div x-show="expanded" x-transition.opacity.duration.150ms class="space-y-2 border-t border-gray-200 pt-2">
                        <div>
                            <span class="font-medium">💼 Company budget:</span>
                            {{ is_null($app->salary_offered) ? 'Not informed' : 'R$ ' . number_format((float) $app->salary_offered, 2, ',', '.') }}
                        </div>
                        <div>
                            <span class="font-medium">🎯 Expected salary:</span>
                            {{ is_null($app->salary_expected) ? 'Not informed' : 'R$ ' . number_format((float) $app->salary_expected, 2, ',', '.') }}
                        </div>
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
                draggable: '.card',
                filter: 'details, summary, button',
                preventOnFilter: false,
                onEnd: function(evt) {
                    if (!evt?.item?.dataset) {
                        return
                    }

                    const id = evt.item.dataset.id
                    const newStatus = evt.to.id

                    if (!id || !newStatus) {
                        return
                    }

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