<div class="p-6">
    @php
        $columns = [
            'applied' => [
                'label' => 'Applied',
                'items' => $applied,
                'total' => $columnTotals['applied'] ?? $applied->count(),
                'remaining' => $columnRemaining['applied'] ?? 0,
            ],
            'waiting' => [
                'label' => 'Waiting',
                'items' => $waiting,
                'total' => $columnTotals['waiting'] ?? $waiting->count(),
                'remaining' => $columnRemaining['waiting'] ?? 0,
            ],
            'interview' => [
                'label' => 'Interview',
                'items' => $interview,
                'total' => $columnTotals['interview'] ?? $interview->count(),
                'remaining' => $columnRemaining['interview'] ?? 0,
            ],
            'rejected' => [
                'label' => 'Rejected',
                'items' => $rejected,
                'total' => $columnTotals['rejected'] ?? $rejected->count(),
                'remaining' => $columnRemaining['rejected'] ?? 0,
            ],
            'offer' => [
                'label' => 'Offer',
                'items' => $offer,
                'total' => $columnTotals['offer'] ?? $offer->count(),
                'remaining' => $columnRemaining['offer'] ?? 0,
            ],
        ];
    @endphp

    @if (session('status'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 2200)" x-show="show"
            class="fixed right-6 top-6 z-50 rounded-2xl bg-gray-900 px-4 py-3 text-sm font-medium text-white shadow-xl">
            {{ session('status') }}
        </div>
    @endif

    @unless ($isBoardPage)
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            @php
                $today = now();
                $todayInterviews = $interview
                    ->filter(fn($app) => $app->interview_date && $app->interview_date->isSameDay($today))
                    ->sortBy('interview_time')
                    ->values();

                $offerRate = $total > 0 ? round(($offers / $total) * 100) : 0;
                $interviewRate = $total > 0 ? round(($interviews / $total) * 100) : 0;
            @endphp

            <div class="mb-4 flex flex-wrap gap-3 rounded-2xl border border-slate-200 bg-white/80 px-5 py-4 shadow-sm">
                <div class="min-w-0 flex-1">
                    <p class="text-lg font-bold text-slate-800">
                        Hi, {{ auth()->user()->name ?? 'there' }}!
                    </p>
                    <p class="mt-1 text-md font-medium text-slate-600">
                        Follow your job opportunities and applications in real time.
                    </p>
                </div>

                <div class="flex min-w-[18rem] md:min-w-[22rem] items-center justify-center rounded-2xl border border-slate-300 bg-gradient-to-br from-slate-50 to-white px-6 py-2 text-center">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Today</p>
                        <p class="mt-1 text-base font-black text-slate-800">{{ $today->format('l') }}</p>
                        <p class="text-sm font-semibold text-slate-600">{{ $today->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <section
                    class="rounded-3xl border border-slate-300/80 bg-gradient-to-br from-white via-slate-50 to-slate-100 p-5 shadow-sm">
                    <div class="flex items-start gap-3 overflow-x-auto">
                        <div class="shrink-0">
                            <h3 class="text-sm font-black uppercase tracking-[0.18em] text-slate-700">Daily reminders</h3>
                            <p class="mt-1 text-xs text-slate-500">Stay on track with today's interview schedule.</p>
                        </div>

                        <div class="flex shrink-0 items-stretch gap-2">
                            <div class="flex w-44 self-stretch items-center justify-center rounded-2xl border border-amber-300 bg-amber-50 px-3 text-center">
                                <div>
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-amber-700">Interviews today</p>
                                    <p class="mt-1 text-3xl font-black text-amber-700">{{ $todayInterviews->count() }}</p>
                                </div>
                            </div>

                            <div class="w-[24rem] md:w-[40rem] rounded-2xl border border-slate-200 bg-white px-4 py-3">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-600">Today schedule</p>

                                @if ($todayInterviews->isEmpty())
                                    <p class="mt-2 text-sm text-slate-500">No interviews scheduled for today.</p>
                                @else
                                    <div class="mt-2 grid grid-cols-1 gap-2 md:grid-cols-2">
                                        @foreach ($todayInterviews->take(2) as $app)
                                            <div
                                                class="flex h-full items-start gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5">
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-semibold leading-tight text-slate-800">
                                                        {{ $app->position }} · {{ $app->company }}</p>
                                                    <p class="mt-1 text-xs text-slate-500">
                                                        {{ $app->interview_is_remote ? 'Remote' : ($app->interview_location ?: 'Interview') }}
                                                    </p>
                                                </div>
                                                <span
                                                    class="rounded-full border border-amber-300 bg-amber-100 px-2 py-0.5 text-[11px] font-bold text-amber-700 whitespace-nowrap">
                                                    {{ $app->interview_time ? \Illuminate\Support\Carbon::parse($app->interview_time)->format('H:i') : 'TBA' }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </section>

                <x-dashboard.dashboard-container>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-5">
                        <div class="rounded-2xl bg-white p-5 shadow">
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total applications
                            </div>
                            <div class="mt-3 text-5xl font-black leading-none text-blue-700">{{ $total }}</div>
                        </div>

                        <div class="rounded-2xl bg-white p-5 shadow">
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Interviews</div>
                            <div class="mt-3 text-5xl font-black leading-none text-amber-600">{{ $interviews }}</div>
                            <div class="mt-2 text-xs font-semibold text-amber-700">Interview rate: {{ $interviewRate }}%
                            </div>
                        </div>

                        <div class="rounded-2xl bg-white p-5 shadow">
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Offers</div>
                            <div class="mt-3 text-5xl font-black leading-none text-emerald-600">{{ $offers }}</div>
                            <div class="mt-2 text-xs font-semibold text-emerald-700">Offer rate: {{ $offerRate }}%</div>
                        </div>

                        <button type="button" wire:click="toggleFavoritesFilter"
                            class="rounded-2xl bg-white p-5 text-left shadow transition border {{ $showFavoritesOnly ? 'border-yellow-400 ring-2 ring-yellow-200' : 'border-transparent hover:border-yellow-300' }}">
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Favorite jobs</div>
                            <div class="mt-3 flex items-end gap-2">
                                <span class="text-5xl font-black leading-none text-yellow-500">{{ $favorites }}</span>
                                <span class="text-lg leading-none text-yellow-500">★</span>
                            </div>

                            <div class="mt-2 text-xs text-gray-500">
                                Click to {{ $showFavoritesOnly ? 'show all jobs' : 'filter only favorites' }}
                            </div>
                        </button>

                        <button type="button" wire:click="toggleArchivedSection"
                            class="rounded-2xl bg-white p-5 text-left shadow transition border {{ $showArchivedSection ? 'border-gray-500 ring-2 ring-gray-200' : 'border-transparent hover:border-gray-300' }}">
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Archived Jobs</div>
                            <div class="mt-3 flex items-end gap-2">
                                <span class="text-5xl font-black leading-none text-slate-600">{{ $archivedCount }}</span>
                                <span class="text-lg leading-none text-slate-500">🗂️</span>
                            </div>

                            <div class="mt-2 text-xs text-gray-500">
                                Click to {{ $showArchivedSection ? 'hide archived list' : 'show archived list' }}
                            </div>
                        </button>

                        <x-dashboard.duplicates-card :duplicateCount="$duplicateCount" :showDuplicates="$showDuplicates" />
                    </div>
                </x-dashboard.dashboard-container>

                <x-dashboard.applications-calendar-card :calendarApplications="$calendarApplications" />


            </div>

            @if ($showFavoritesOnly)
                <div class="my-4 flex items-center" aria-hidden="true">
                    <div class="h-px w-full bg-gray-300"></div>
                </div>
                <div
                    class="mt-4 flex items-center justify-between rounded-xl border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-900">
                    <span>Showing only favorite applications.</span>
                    <button type="button" wire:click="clearFavoritesFilter" class="font-semibold underline">
                        Clear filter
                    </button>
                </div>

                @php
                    $favoriteApplications = collect([$applied, $waiting, $interview, $rejected, $offer])
                        ->flatten(1)
                        ->filter(fn($app) => (bool) ($app->is_favorite ?? false))
                        ->unique('id')
                        ->values();
                @endphp

                <div class="mt-4 rounded-2xl border border-yellow-200 bg-white p-4 shadow-sm">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Favorite applications</h2>
                        <span class="rounded-full bg-yellow-50 px-3 py-1 text-xs font-semibold text-yellow-700">
                            {{ $favoriteApplications->count() }} items
                        </span>
                    </div>

                    @if ($favoriteApplications->isEmpty())
                        <div
                            class="rounded-xl border border-dashed border-yellow-200 bg-yellow-50 px-4 py-6 text-center text-sm text-yellow-800">
                            No favorite applications found.
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
                            @foreach ($favoriteApplications as $app)
                                <article class="rounded-2xl border border-yellow-100 bg-yellow-50/40 px-4 py-3"
                                    wire:key="favorite-{{ $app->id }}">
                                    <div class="text-sm font-semibold text-gray-900 uppercase">{{ $app->position }}</div>
                                    <div class="text-sm text-gray-700">{{ $app->company }}</div>
                                    <div class="mt-1 text-xs text-gray-500">Applied:
                                        {{ $app->applied_at?->format('d/m/Y') ?? '-' }}</div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            @if ($showArchivedSection)
                <div class="my-4 flex items-center" aria-hidden="true">
                    <div class="h-px w-full bg-gray-300"></div>
                </div>
                <div
                    class="mt-4 flex items-center justify-between rounded-xl border border-black-800 bg-gray-100 px-4 py-3 text-sm text-gray-800">
                    <span>Showing only archived applications</span>
                    <button type="button" wire:click="clearArchivedsFilter" class="font-semibold underline">
                        Clear filter
                    </button>
                </div>

                <div class="mt-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Archived applications</h2>
                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                            {{ $archivedCount }} items
                        </span>
                    </div>

                    @if ($archived->isEmpty())
                        <div
                            class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-center text-sm text-gray-500">
                            No archived applications yet.
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
                            @foreach ($archived as $app)
                                <article class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3"
                                    wire:key="archived-{{ $app->id }}">
                                    <div class="text-sm font-semibold text-gray-900 uppercase">{{ $app->position }}</div>
                                    <div class="text-sm text-gray-700">{{ $app->company }}</div>
                                    <div class="mt-1 text-xs text-gray-500">Applied:
                                        {{ $app->applied_at?->format('d/m/Y') ?? '-' }}</div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            @if ($showDuplicates)
                <div class="my-4 flex items-center" aria-hidden="true">
                    <div class="h-px w-full bg-gray-300"></div>
                </div>
                <div
                    class="mt-4 flex items-center justify-between rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <span>Showing only duplicated applications. A card is marked as duplicated when it has the same key
                        information as another one.</span>
                    <button type="button" wire:click="clearDuplicatesFilter" class="font-semibold underline">
                        Clear filter
                    </button>
                </div>
                @php
                    $duplicatedApplications = collect([$applied, $waiting, $interview, $rejected, $offer])
                        ->flatten(1)
                        ->unique('id')
                        ->values();
                @endphp

                <div class="mt-4 rounded-2xl border border-red-200 bg-white p-4 shadow-sm">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Duplicated applications</h2>
                        <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">
                            {{ $duplicatedApplications->count() }} items
                        </span>
                    </div>

                    @if ($duplicatedApplications->isEmpty())
                        <div
                            class="rounded-xl border border-dashed border-red-200 bg-red-50 px-4 py-6 text-center text-sm text-red-700">
                            No duplicated applications found.
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
                            @foreach ($duplicatedApplications as $app)
                                <article class="rounded-2xl border border-red-100 bg-red-50/40 px-4 py-3"
                                    wire:key="duplicated-{{ $app->id }}">
                                    <div class="text-sm font-semibold text-gray-900 uppercase">{{ $app->position }}</div>
                                    <div class="text-sm text-gray-700">{{ $app->company }}</div>
                                    <div class="mt-1 text-xs text-gray-500">Applied:
                                        {{ $app->applied_at?->format('d/m/Y') ?? '-' }}</div>
                                    <div class="mt-1 text-xs text-red-700">
                                        {{ $duplicateReasons[$app->id] ?? 'Duplicate by matching data with another application.' }}
                                    </div>
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
        </div>
    @endunless

    @if ($isBoardPage)
        <x-modals.create-application-modal :isOpen="$isCreateFormOpen" :company="$company" :position="$position" :city="$city"
            :location="$location" :appliedAt="$applied_at" :jobUrl="$job_url" :personalScore="$personal_score" :salaryOffered="$salary_offered"
            :salaryExpected="$salary_expected" />

        <div class="mb-8 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div class="relative w-full md:max-w-md">
                    <input type="text" wire:model.live.debounce.400ms="searchQuery"
                        placeholder="Search in kanban..."
                        class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 pr-10 text-sm text-gray-700 focus:border-[#415A77] focus:outline-none focus:ring-2 focus:ring-[#415A77]/25" />
                    <svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                        viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path
                            d="M21 21L16.65 16.65M19 11C19 15.4183 15.4183 19 11 19C6.58172 19 3 15.4183 3 11C3 6.58172 6.58172 3 11 3C15.4183 3 19 6.58172 19 11Z"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </div>

                <button type="button" wire:click="toggleKanbanOrientation" wire:loading.attr="disabled"
                    wire:loading.class="cursor-not-allowed opacity-60" wire:target="toggleKanbanOrientation"
                    aria-label="Toggle kanban orientation"
                    class="group relative inline-flex h-12 w-12 items-center justify-center rounded-xl border border-gray-300 bg-white text-gray-700 shadow-sm transition hover:bg-gray-100">
                    <svg class="h-5 w-5 transition-transform duration-300 ease-out"
                        style="transform: rotate({{ $kanbanOrientation === 'vertical' ? 90 : 0 }}deg)"
                        viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M8 7H19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M16 4L19 7L16 10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M16 17H5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M8 14L5 17L8 20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>

                    <span
                        class="pointer-events-none absolute right-0 top-full z-20 mt-2 whitespace-nowrap rounded-lg bg-gray-900 px-2.5 py-1.5 text-xs font-medium text-white opacity-0 transition-opacity duration-150 group-hover:opacity-100">
                        {{ $kanbanOrientation === 'horizontal' ? 'Switch to vertical view' : 'Switch to horizontal view' }}
                    </span>
                </button>
            </div>

            <div class="grid grid-cols-1 gap-6 {{ $kanbanOrientation === 'horizontal' ? 'xl:grid-cols-5' : '' }}">
                @foreach ($columns as $status => $column)
                    <div data-dropzone
                        class="rounded-2xl border border-gray-200 bg-gray-50 p-4 transition-colors duration-150">
                        <div class="mb-3 flex items-center justify-between gap-3">
                            <h2 class="font-bold text-gray-900">{{ $column['label'] }}</h2>
                            <span
                                class="inline-flex min-w-8 items-center justify-center rounded-full border border-gray-200 bg-white px-2.5 py-1 text-sm font-semibold text-gray-700">
                                {{ $column['total'] }}
                            </span>
                        </div>

                        <div id="{{ $status }}" class="min-h-24 space-y-3">
                            @foreach ($column['items'] as $app)
                                <article class="card rounded-2xl border border-gray-200 bg-white p-4 shadow-sm"
                                    data-id="{{ $app->id }}" wire:key="application-{{ $app->id }}"
                                    style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;"
                                    x-data="{ expanded: false }">
                                    <div class="mb-3 flex items-start justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-3">
                                                @if ($hasFavoriteColumn)
                                                    <div
                                                        class="flex h-10 w-10 min-h-10 min-w-10 max-h-10 max-w-10 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white">
                                                        <button type="button"
                                                            wire:click="toggleFavorite({{ $app->id }})"
                                                            class="flex h-full w-full items-center justify-center text-2xl leading-none transition {{ $app->is_favorite ? 'text-yellow-500' : 'text-gray-300 hover:text-yellow-400' }}"
                                                            title="Mark as favorite" aria-label="Toggle favorite">
                                                            ★
                                                        </button>
                                                    </div>
                                                @endif

                                                <div class="text-sm font-semibold uppercase text-blue-700">
                                                    {{ $app->position }}
                                                </div>
                                            </div>
                                            @php
                                                $isInterviewToday = $app->interview_date?->isToday();
                                                $isInterviewTomorrow = $app->interview_date?->isTomorrow();
                                                $isDuplicate = in_array($app->id, $duplicateIds ?? [], true);
                                                $appliedDateLabel = $app->applied_at?->format('M d, Y');
                                                $appliedRelativeLabel = $app->applied_at
                                                    ?->copy()
                                                    ->locale('en')
                                                    ->diffForHumans();
                                            @endphp
                                            <div class="mt-2 text-xs text-gray-400">
                                                @if ($appliedDateLabel)
                                                    Applied {{ $appliedDateLabel }}
                                                    @if ($appliedRelativeLabel)
                                                        • {{ $appliedRelativeLabel }}
                                                    @endif
                                                @else
                                                    Applied -
                                                @endif
                                            </div>
                                        </div>

                                        <div class="flex shrink-0 flex-col items-end gap-2">
                                            <details class="card-actions relative">
                                                <summary
                                                    class="cursor-pointer list-none rounded-xl border border-gray-200 px-2.5 py-1 text-lg leading-none text-gray-500 transition hover:bg-gray-100 hover:text-gray-700">
                                                    ...
                                                </summary>

                                                <div
                                                    class="absolute right-0 z-10 mt-2 w-36 rounded-xl border border-gray-200 bg-white p-2 shadow-lg">
                                                    <button type="button"
                                                        wire:click="editApplication({{ $app->id }})"
                                                        class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-100">
                                                        Edit
                                                    </button>
                                                    <button type="button"
                                                        wire:click="deleteApplication({{ $app->id }})"
                                                        wire:confirm="Delete this application?"
                                                        class="block w-full rounded-lg px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50">
                                                        Delete
                                                    </button>
                                                </div>
                                            </details>

                                            @if ($isDuplicate)
                                                <span
                                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold bg-yellow-200 text-gray-700 ring-1 ring-gray-300">
                                                    Duplicate
                                                </span>
                                            @endif

                                            @if ($app->interview_date && ($isInterviewToday || $isInterviewTomorrow))
                                                <span
                                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $isInterviewToday ? 'bg-red-100 text-red-700 ring-1 ring-red-200' : 'bg-orange-100 text-orange-700 ring-1 ring-orange-200' }}">
                                                    {{ $isInterviewToday ? 'Today' : 'Tomorrow' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="space-y-2 text-sm text-gray-700">
                                        @if ($showDuplicates && $isDuplicate)
                                            <div
                                                class="rounded-lg border border-gray-200 bg-gray-100 px-2.5 py-2 text-xs text-gray-700">
                                                {{ $duplicateReasons[$app->id] ?? 'Duplicate by matching data with another application.' }}
                                            </div>
                                        @endif
                                        <div>
                                            <span class="font-medium">Company name:</span>
                                            {{ $app->company }}
                                        </div>
                                        <div>
                                            <span class="font-medium">City:</span>
                                            {{ $app->city ?: 'Not informed' }}
                                        </div>
                                        <div>
                                            <span class="font-medium">Work model:</span>
                                            {{ $app->location ?: 'Not informed' }}
                                        </div>

                                        <div class="flex items-center justify-between gap-3">
                                            <div>
                                                <span class="font-medium">Personal score:</span>
                                                {{ is_null($app->personal_score) ? 'Not rated' : $app->personal_score . '/10' }}
                                            </div>
                                            <button type="button" @click="expanded = !expanded"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:bg-gray-100 hover:text-gray-700"
                                                :aria-expanded="expanded" aria-label="Toggle job details"
                                                title="Show more details">
                                                <svg class="h-4 w-4 transition-transform duration-200"
                                                    :class="expanded ? 'rotate-180' : ''" viewBox="0 0 20 20"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg"
                                                    aria-hidden="true">
                                                    <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor"
                                                        stroke-width="1.8" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                        </div>

                                        <div x-show="expanded" x-transition.opacity.duration.150ms
                                            class="space-y-2 border-t border-gray-200 pt-2">
                                            <div>
                                                <span class="font-medium">Company budget:</span>
                                                {{ is_null($app->salary_offered) ? 'Not informed' : 'R$ ' . number_format((float) $app->salary_offered, 2, ',', '.') }}
                                            </div>
                                            <div>
                                                <span class="font-medium">Expected salary:</span>
                                                {{ is_null($app->salary_expected) ? 'Not informed' : 'R$ ' . number_format((float) $app->salary_expected, 2, ',', '.') }}
                                            </div>
                                            @if (
                                                $app->status === 'interview' &&
                                                    ($app->interview_date ||
                                                        $app->interview_time ||
                                                        $app->interview_location ||
                                                        $app->interview_platform ||
                                                        $app->interview_address))
                                                <div class="my-2 border-t border-gray-200"></div>
                                                <div>
                                                    <span class="font-medium">Interview:</span>
                                                    {{ $app->interview_date?->format('d/m/Y') ?? 'Date not set' }}
                                                    @if ($app->interview_time)
                                                        at {{ $app->interview_time }}
                                                    @endif
                                                </div>
                                                @if ($app->interview_location)
                                                    <div>
                                                        <span class="font-medium">Interview location:</span>
                                                        {{ $app->interview_location }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <span class="font-medium">Format:</span>
                                                    {{ $app->interview_is_remote ? 'Remote' : 'In person' }}
                                                </div>
                                                @if ($app->interview_is_remote && $app->interview_platform)
                                                    <div>
                                                        <span class="font-medium">Platform:</span>
                                                        {{ $app->interview_platform }}
                                                    </div>
                                                @endif
                                                @if (!$app->interview_is_remote && $app->interview_address)
                                                    <div>
                                                        <span class="font-medium">Address:</span>
                                                        {{ $app->interview_address }}
                                                    </div>
                                                @endif
                                            @endif
                                            @if ($app->notes)
                                                <div>
                                                    <span class="font-medium">Notes:</span>
                                                    {{ $app->notes }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </article>
                            @endforeach

                            @if ($column['items']->isEmpty())
                                <div
                                    class="rounded-2xl border border-dashed border-gray-300 bg-white/60 px-4 py-6 text-center text-sm text-gray-400">
                                    No applications in this column yet.
                                </div>
                            @endif

                            @if ($isBoardPage && ($column['remaining'] ?? 0) > 0)
                                <div class="pt-2">
                                    <button type="button" wire:click="loadMore('{{ $status }}')"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-wide text-slate-600 transition hover:bg-slate-50">
                                        Load more ({{ $column['remaining'] }})
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <x-modals.edit-application-modal :isOpen="$isEditModalOpen" :company="$editCompany" :position="$editPosition" :city="$editCity"
            :location="$editLocation" :appliedAt="$editAppliedAt" :jobUrl="$editJobUrl" :personalScore="$editPersonalScore" :salaryOffered="$editSalaryOffered"
            :salaryExpected="$editSalaryExpected" :notes="$editNotes" :editingIsInterview="$editingIsInterview" :interviewDate="$editInterviewDate" :interviewTime="$editInterviewTime"
            :interviewIsRemote="$editInterviewIsRemote" :interviewPlatform="$editInterviewPlatform" :interviewAddress="$editInterviewAddress" :currentStatus="$editingCurrentStatus" />

        <x-modals.interview-scheduling-modal :isOpen="$isInterviewModalOpen" :interviewDate="$interviewDate" :interviewTime="$interviewTime" :interviewIsRemote="$interviewIsRemote"
            :interviewPlatform="$interviewPlatform" :interviewAddress="$interviewAddress" />
    @endif

</div>

@if ($isBoardPage)
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>
        function initSortables() {
            const receivingColumnClasses = ['bg-gray-200/70', 'border-gray-500']

            function clearReceivingListHover() {
                ['applied', 'waiting', 'interview', 'rejected', 'offer'].forEach((status) => {
                    const list = document.getElementById(status)
                    if (!list) return

                    const column = list.closest('[data-dropzone]')
                    if (!column) return

                    column.classList.remove(...receivingColumnClasses)
                })
            }

            function setReceivingListHover(targetList) {
                clearReceivingListHover()
                if (!targetList) return

                const column = targetList.closest('[data-dropzone]')
                if (!column) return

                column.classList.add(...receivingColumnClasses)
            }

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
                    onStart: function() {
                        clearReceivingListHover()
                    },
                    onMove: function(evt) {
                        setReceivingListHover(evt?.to)
                    },
                    onEnd: function(evt) {
                        clearReceivingListHover()

                        if (!evt?.item?.dataset) {
                            return
                        }

                        const id = evt.item.dataset.id
                        const newStatus = evt.to.id

                        if (!id || !newStatus) {
                            return
                        }

                        const orderedIds = Array.from(evt.to.querySelectorAll('.card'))
                            .map((card) => Number(card.dataset.id))
                            .filter(Boolean)

                        if (evt.from.id !== 'interview' && newStatus === 'interview') {
                            const referenceNode = evt.from.children[evt.oldIndex] ?? null
                            evt.from.insertBefore(evt.item, referenceNode)

                            Livewire.dispatch('prepareInterviewMove', {
                                id: id
                            })
                            return
                        }

                        // Livewire v3/v4: dispatch with named payload { id, status }
                        Livewire.dispatch('moveApplication', {
                            id: id,
                            status: newStatus,
                            orderedIds: orderedIds
                        })
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
@endif
