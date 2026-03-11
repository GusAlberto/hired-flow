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

    <div
        x-data="kanbanToggleDock(@js($kanbanTogglePosition))"
        class="mb-4"
    >
        <div x-ref="track" class="relative h-12 w-full select-none">
            <div
                class="pointer-events-none absolute left-0 top-0 h-12 w-12 rounded-xl border border-dashed bg-white/70 transition-all duration-200"
                :class="dragging
                    ? (previewSide() === 'left'
                        ? 'border-blue-300 shadow-lg ring-4 ring-blue-100'
                        : 'border-gray-300 shadow-md opacity-90')
                    : 'opacity-0'"
                aria-hidden="true"
            ></div>

            <div
                class="pointer-events-none absolute right-0 top-0 h-12 w-12 rounded-xl border border-dashed bg-white/70 transition-all duration-200"
                :class="dragging
                    ? (previewSide() === 'right'
                        ? 'border-blue-300 shadow-lg ring-4 ring-blue-100'
                        : 'border-gray-300 shadow-md opacity-90')
                    : 'opacity-0'"
                aria-hidden="true"
            ></div>

            <button
                x-ref="button"
                type="button"
                @pointerdown="startDrag($event)"
                @pointermove.window="onDrag($event)"
                @pointerup.window="endDrag($event)"
                @pointercancel.window="cancelDrag()"
                wire:loading.attr="disabled"
                wire:loading.class="cursor-not-allowed opacity-60"
                wire:target="toggleKanbanOrientation,setKanbanTogglePosition"
                aria-label="Toggle kanban orientation"
                class="group absolute top-0 inline-flex h-12 w-12 touch-none items-center justify-center rounded-xl border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-100"
                :class="dragging
                    ? 'cursor-grabbing scale-105 shadow-lg transition-none'
                    : 'cursor-grab transition-[left,background-color,opacity,transform,box-shadow] duration-300 [transition-timing-function:cubic-bezier(0.34,1.56,0.64,1)]'"
                style="will-change: left, transform;"
                :style="buttonStyle()"
            >
                <svg
                    class="h-5 w-5 transition-transform duration-300 ease-out"
                    style="transform: rotate({{ $kanbanOrientation === 'vertical' ? 90 : 0 }}deg)"
                    viewBox="0 0 24 24"
                            ? 'border-blue-300 shadow-xl ring-4 ring-blue-100 scale-110 opacity-100'
                            : 'border-gray-300 shadow-md opacity-90 scale-100')
                    aria-hidden="true"
                >
                    <path d="M8 7H19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M16 4L19 7L16 10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M16 17H5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M8 14L5 17L8 20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                </svg>

                <span class="pointer-events-none absolute left-1/2 top-full z-20 mt-2 -translate-x-1/2 whitespace-nowrap rounded-lg bg-gray-900 px-2.5 py-1.5 text-xs font-medium text-white opacity-0 transition-opacity duration-150 group-hover:opacity-100">
                    {{ $kanbanOrientation === 'horizontal' ? 'Switch to vertical view' : 'Switch to horizontal view' }}
                </span>
            </button>
        </div>
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
                                            ? 'border-blue-300 shadow-xl ring-4 ring-blue-100 scale-110 opacity-100'
                                            : 'border-gray-300 shadow-md opacity-90 scale-100')
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
    function kanbanToggleDock(initialSide) {
        return {
            side: initialSide === 'left' ? 'left' : 'right',
            dragging: false,
            didDrag: false,
            pointerId: null,
            currentLeft: null,
            startX: 0,
            startY: 0,
            grabOffsetX: 0,
            pressStartedAt: 0,
            dragThreshold: 8,

            buttonStyle() {
                const left = this.currentLeft ?? this.dockedLeft()
                return `left: ${left}px;`
            },

            startDrag(event) {
                this.dragging = false
                this.didDrag = false
                this.pointerId = event.pointerId
                this.startX = event.clientX
                this.startY = event.clientY
                this.pressStartedAt = Date.now()
                this.currentLeft = null
                this.grabOffsetX = event.clientX - this.$refs.button.getBoundingClientRect().left

                if (this.$refs.button.setPointerCapture) {
                    this.$refs.button.setPointerCapture(event.pointerId)
                }
            },

            onDrag(event) {
                if (event.pointerId !== this.pointerId) {
                    return
                }

                const deltaX = event.clientX - this.startX
                const deltaY = event.clientY - this.startY
                const distance = Math.hypot(deltaX, deltaY)

                if (!this.dragging && distance <= this.dragThreshold) {
                    return
                }

                if (!this.dragging) {
                    this.dragging = true
                    this.currentLeft = this.dockedLeft()
                }

                const nextLeft = this.pointerToLeft(event.clientX)
                this.currentLeft = nextLeft
                this.didDrag = true
            },

            endDrag(event) {
                if (event.pointerId !== this.pointerId) {
                    return
                }

                if (this.didDrag) {
                    const nextSide = this.previewSide()
                    this.side = nextSide
                    this.$wire.setKanbanTogglePosition(nextSide)
                } else {
                    const pressDuration = Date.now() - this.pressStartedAt

                    // Quick tap toggles orientation. Press-and-hold does not.
                    if (pressDuration < 220) {
                        this.$wire.toggleKanbanOrientation()
                    }
                }

                if (this.$refs.button.releasePointerCapture && this.$refs.button.hasPointerCapture?.(event.pointerId)) {
                    this.$refs.button.releasePointerCapture(event.pointerId)
                }

                this.dragging = false
                this.pointerId = null
                this.currentLeft = null
                this.startY = 0
                this.grabOffsetX = 0
                this.pressStartedAt = 0
            },

            cancelDrag() {
                if (this.pointerId !== null && this.$refs.button.releasePointerCapture && this.$refs.button.hasPointerCapture?.(this.pointerId)) {
                    this.$refs.button.releasePointerCapture(this.pointerId)
                }

                this.dragging = false
                this.pointerId = null
                this.currentLeft = null
                this.startY = 0
                this.grabOffsetX = 0
                this.pressStartedAt = 0
                this.didDrag = false
            },

            pointerToLeft(clientX) {
                const trackRect = this.$refs.track.getBoundingClientRect()
                const rawLeft = clientX - trackRect.left - this.grabOffsetX

                return Math.min(Math.max(rawLeft, 0), this.maxLeft())
            },

            buttonCenter() {
                const left = this.currentLeft ?? this.dockedLeft()
                return left + (this.buttonWidth() / 2)
            },

            previewSide() {
                return this.buttonCenter() < this.trackMidpoint() ? 'left' : 'right'
            },

            trackMidpoint() {
                return this.$refs.track.clientWidth / 2
            },

            buttonWidth() {
                return this.$refs.button.offsetWidth
            },

            maxLeft() {
                return Math.max(this.$refs.track.clientWidth - this.buttonWidth(), 0)
            },

            dockedLeft() {
                return this.side === 'left' ? 0 : this.maxLeft()
            },
        }
    }

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