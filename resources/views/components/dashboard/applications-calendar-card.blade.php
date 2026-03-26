@props(['calendarApplications' => []])

<section
    x-data='{
        open: false,
        byDate: {},
        weekdays: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
        currentYear: (new Date()).getFullYear(),
        currentMonth: (new Date()).getMonth() + 1,
        selectedDate: "",
        init() {
            const applications = JSON.parse(this.$refs.calendarData.textContent || "[]")
            this.byDate = applications.reduce((acc, item) => {
                if (!item || !item.applied_at) return acc
                if (!acc[item.applied_at]) acc[item.applied_at] = []
                acc[item.applied_at].push(item)
                return acc
            }, {})

            const todayIso = this.formatIso(new Date())
            const dates = Object.keys(this.byDate).sort()
            const reference = dates[0] || todayIso
            const [year, month] = reference.split("-").map(Number)

            this.currentYear = year
            this.currentMonth = month
            this.selectedDate = this.byDate[todayIso] ? todayIso : (dates[0] || todayIso)
        },
        formatIso(date) {
            const y = date.getFullYear()
            const m = String(date.getMonth() + 1).padStart(2, "0")
            const d = String(date.getDate()).padStart(2, "0")
            return `${y}-${m}-${d}`
        },
        monthLabel() {
            const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]
            return `${monthNames[this.currentMonth - 1]} ${this.currentYear}`
        },
        get monthCells() {
            const firstDay = new Date(this.currentYear, this.currentMonth - 1, 1)
            const daysInMonth = new Date(this.currentYear, this.currentMonth, 0).getDate()
            const startWeekday = firstDay.getDay()
            const cells = []

            for (let i = 0; i < startWeekday; i += 1) {
                cells.push({ key: `blank-${i}`, day: null, iso: null, count: 0 })
            }

            for (let day = 1; day <= daysInMonth; day += 1) {
                const iso = this.formatIso(new Date(this.currentYear, this.currentMonth - 1, day))
                cells.push({ key: iso, day, iso, count: (this.byDate[iso] || []).length })
            }

            return cells
        },
        get selectedItems() {
            return this.byDate[this.selectedDate] || []
        },
        get selectedLabel() {
            if (!this.selectedDate) return "No date selected"
            const [y, m, d] = this.selectedDate.split("-").map(Number)
            return `${String(d).padStart(2, "0")}/${String(m).padStart(2, "0")}/${y}`
        },
        dayClass(cell) {
            if (!cell.iso) return "cursor-default border-transparent bg-transparent text-transparent"
            const isSelected = this.selectedDate === cell.iso
            if (isSelected) return "border-cyan-500 bg-cyan-50 text-cyan-900 shadow-sm"
            if (cell.count > 0) return "border-cyan-200 bg-cyan-50/70 text-cyan-800 hover:border-cyan-400 hover:bg-cyan-100"
            return "border-slate-200 bg-white text-slate-500 hover:border-slate-300 hover:bg-slate-50"
        },
        selectDate(iso) {
            this.selectedDate = iso
        },
        prevMonth() {
            if (this.currentMonth === 1) {
                this.currentMonth = 12
                this.currentYear -= 1
                return
            }
            this.currentMonth -= 1
        },
        nextMonth() {
            if (this.currentMonth === 12) {
                this.currentMonth = 1
                this.currentYear += 1
                return
            }
            this.currentMonth += 1
        }
    }'
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

        <div class="relative flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-cyan-700">Application calendar</p>
                <h3 class="mt-2 text-2xl font-black text-slate-900">Pick a day and review your applications</h3>
                <p class="mt-1 text-sm text-slate-600">Click this card to expand a full monthly view and see jobs applied on each date.</p>
            </div>

            <div class="rounded-2xl border border-cyan-200 bg-white/90 px-4 py-3 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Tracked dates</div>
                <div class="mt-1 text-3xl font-black text-cyan-700" x-text="Object.keys(byDate).length"></div>
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
