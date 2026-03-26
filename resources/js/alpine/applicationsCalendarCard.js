export function applicationsCalendarCardFactory() {
    return {
        open: false,
        byDate: {},
        weekdays: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        currentYear: new Date().getFullYear(),
        currentMonth: new Date().getMonth() + 1,
        selectedDate: '',

        init() {
            const applications = JSON.parse(this.$refs.calendarData?.textContent || '[]');

            this.byDate = applications.reduce((acc, item) => {
                if (!item || !item.applied_at) return acc;
                if (!acc[item.applied_at]) acc[item.applied_at] = [];
                acc[item.applied_at].push(item);
                return acc;
            }, {});

            const todayIso = this.formatIso(new Date());
            const dates = Object.keys(this.byDate).sort();
            const reference = dates[0] || todayIso;
            const [year, month] = reference.split('-').map(Number);

            this.currentYear = year;
            this.currentMonth = month;
            this.selectedDate = this.byDate[todayIso] ? todayIso : (dates[0] || todayIso);
        },

        formatIso(date) {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        },

        monthLabel() {
            const monthNames = [
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December',
            ];

            return `${monthNames[this.currentMonth - 1]} ${this.currentYear}`;
        },

        get monthCells() {
            const firstDay = new Date(this.currentYear, this.currentMonth - 1, 1);
            const daysInMonth = new Date(this.currentYear, this.currentMonth, 0).getDate();
            const startWeekday = firstDay.getDay();
            const cells = [];

            for (let i = 0; i < startWeekday; i += 1) {
                cells.push({ key: `blank-${i}`, day: null, iso: null, count: 0 });
            }

            for (let day = 1; day <= daysInMonth; day += 1) {
                const iso = this.formatIso(new Date(this.currentYear, this.currentMonth - 1, day));
                cells.push({ key: iso, day, iso, count: (this.byDate[iso] || []).length });
            }

            return cells;
        },

        get selectedItems() {
            return this.byDate[this.selectedDate] || [];
        },

        get selectedLabel() {
            if (!this.selectedDate) return 'No date selected';
            const [y, m, d] = this.selectedDate.split('-').map(Number);
            return `${String(d).padStart(2, '0')}/${String(m).padStart(2, '0')}/${y}`;
        },

        dayClass(cell) {
            if (!cell.iso) return 'cursor-default border-transparent bg-transparent text-transparent';

            const isSelected = this.selectedDate === cell.iso;
            if (isSelected) return 'border-cyan-500 bg-cyan-50 text-cyan-900 shadow-sm';
            if (cell.count > 0) return 'border-cyan-200 bg-cyan-50/70 text-cyan-800 hover:border-cyan-400 hover:bg-cyan-100';
            return 'border-slate-200 bg-white text-slate-500 hover:border-slate-300 hover:bg-slate-50';
        },

        selectDate(iso) {
            this.selectedDate = iso;
        },

        prevMonth() {
            if (this.currentMonth === 1) {
                this.currentMonth = 12;
                this.currentYear -= 1;
                return;
            }

            this.currentMonth -= 1;
        },

        nextMonth() {
            if (this.currentMonth === 12) {
                this.currentMonth = 1;
                this.currentYear += 1;
                return;
            }

            this.currentMonth += 1;
        },
    };
}

export function registerApplicationsCalendarCard(Alpine) {
    if (!Alpine) return;

    Alpine.data('applicationsCalendarCard', applicationsCalendarCardFactory);
}

if (typeof window !== 'undefined') {
    window.applicationsCalendarCard = applicationsCalendarCardFactory;
}
