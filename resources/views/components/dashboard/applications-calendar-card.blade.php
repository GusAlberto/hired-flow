@props(['calendarApplications' => []])

<section
    x-data="window.applicationsCalendarCard ? window.applicationsCalendarCard() : {}"
    class="mb-8">
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <script type="application/json" x-ref="calendarData">@json($calendarApplications)</script>

    <button type="button" @click="open = !open"
        class="group relative w-full overflow-hidden rounded-3xl border border-cyan-200/80 bg-gradient-to-br from-cyan-50 via-white to-sky-100 p-5 text-left shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
        <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-cyan-300/30 blur-2xl"></div>
        <div class="pointer-events-none absolute -bottom-12 left-6 h-32 w-32 rounded-full bg-blue-300/20 blur-2xl"></div>

        <div class="relative flex flex-wrap items-start justify-between gap-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-cyan-700">Application calendar</p>
                <h3 class="mt-2 text-2xl font-black text-slate-900">Pick a day and review your applications</h3>
                <p class="mt-1 text-sm text-slate-600">Click this card to expand a full monthly view and see jobs applied on each date.</p>
            </div>

            <div class="relative flex items-start gap-3">
                <!-- Calendar Visual Balloon -->
                {{-- <div class="relative rounded-2xl border border-sky-300 bg-gradient-to-br from-sky-100 to-cyan-100 p-3 shadow-sm">
                    <div class="grid grid-cols-3 gap-1">
                        <div class="h-2 w-2 rounded-sm bg-sky-400"></div>
                        <div class="h-2 w-2 rounded-sm bg-sky-400"></div>
                        <div class="h-2 w-2 rounded-sm bg-sky-600"></div>
                        <div class="h-2 w-2 rounded-sm bg-sky-400"></div>
                        <div class="h-2 w-2 rounded-sm bg-sky-400"></div>
                        <div class="h-2 w-2 rounded-sm bg-sky-400"></div>
                        <div class="h-2 w-2 rounded-sm bg-sky-400"></div>
                        <div class="h-2 w-2 rounded-sm bg-cyan-600"></div>
                        <div class="h-2 w-2 rounded-sm bg-sky-400"></div>
                    </div>
                    
                    <!-- Pointer/Arrow pointing left -->
                    <div class="absolute -right-2 top-1/2 -translate-y-1/2 h-0 w-0 border-l-4 border-t-2 border-b-2 border-l-sky-100 border-t-transparent border-b-transparent"></div>
                </div> --}}

                <!-- Tracked Dates Box -->
                <div class="rounded-2xl border border-cyan-200 bg-white/90 px-4 py-3 shadow-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Tracked dates</span>
                        <svg class="h-4 w-4 text-cyan-600" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
                            <line x1="16" y1="2" x2="16" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <line x1="8" y1="2" x2="8" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <line x1="3" y1="10" x2="21" y2="10" stroke="currentColor" stroke-width="2"/>
                        </svg>
                    </div>
                    <div class="mt-1 text-3xl font-black text-cyan-700" x-text="Object.keys(byDate).length"></div>
                </div>
            </div>
        </div>

        <div class="relative mt-4 flex items-center gap-2 text-sm font-semibold text-cyan-700">
            <span>Open calendar</span>
            <svg class="h-4 w-4 transition group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                <path d="M13 6L19 12L13 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </div>
    </button>

    <div x-show="open" x-cloak x-transition.opacity.duration.180ms @keydown.escape.window="open = false"
        class="relative mt-3" @click.outside="open = false">
        <div class="w-full max-w-xl rounded-3xl border border-cyan-200 bg-white p-4 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <button type="button" @click="prevMonth()"
                    class="inline-flex items-center gap-1 rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                    <span>&larr;</span>
                    <span>Prev</span>
                </button>

                <div class="text-center">
                    <p class="text-base font-black text-slate-900" x-text="monthLabel()"></p>
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Applications calendar</p>
                </div>

                <button type="button" @click="nextMonth()"
                    class="inline-flex items-center gap-1 rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                    <span>Next</span>
                    <span>&rarr;</span>
                </button>
            </div>

            <div class="mb-4 flex gap-2">
                <input type="date" x-model="jumpDate"
                    class="flex-1 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-cyan-300 focus:outline-none focus:ring-2 focus:ring-cyan-200" />

                <button type="button" @click="jumpToDateInput()"
                    class="rounded-lg bg-cyan-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-cyan-700">
                    Go
                </button>
            </div>

            <div class="mb-2 grid grid-cols-7 gap-2 text-center text-[11px] font-bold uppercase tracking-wide text-slate-500">
                <template x-for="day in weekdays" :key="day">
                    <div x-text="day"></div>
                </template>
            </div>

            <div class="grid grid-cols-7 gap-2">
                <template x-for="cell in monthCells" :key="cell.key">
                    <button type="button"
                        @click="cell.iso && selectDate(cell.iso)"
                        :disabled="!cell.iso"
                        class="relative h-10 rounded-lg border text-sm font-semibold transition"
                        :class="dayClass(cell)">
                        <span x-text="cell.day || ''"></span>
                        <span x-show="cell.count > 0"
                            class="absolute -right-1 -top-1 inline-flex min-h-4 min-w-4 items-center justify-center rounded-full bg-cyan-500 px-1 text-[9px] font-bold text-white"
                            x-text="cell.count"></span>
                    </button>
                </template>
            </div>

            <div class="mt-3 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-700">
                <span class="font-semibold" x-text="selectedLabel"></span>
                <span class="mx-1">-</span>
                <span x-text="selectedItems.length + ' applications'"></span>
            </div>
        </div>
    </div>
</section>
