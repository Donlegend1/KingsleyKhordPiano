@extends('layouts.member')

@section('content')

<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8 font-jakarta"
    x-data="{
        currentMonth: new Date().getMonth(),
        currentYear: new Date().getFullYear(),
        selectedDay: null,
        selectedTime: null,
        confirmed: {{ $activeBooking ? 'true' : 'false' }},
        activeBooking: {{ json_encode($activeBooking) }},
        calendarMenuOpen: false,
        booking: false,
        bookingError: '',
        userTz: Intl.DateTimeFormat().resolvedOptions().timeZone,
        userTzLabel: '',
        months: ['January','February','March','April','May','June','July','August','September','October','November','December'],
        days: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],

        sessionsUsed: {{ $sessionsUsed }},
        sessionsIncluded: {{ $sessionsIncluded }},
        nextResetLabel: {{ json_encode($nextResetLabel) }},
        bookedSlotsByDate: {{ json_encode($bookedSlots) }},

        slotsByDow: {
            0: [{start:[9,0],end:[9,45]},  {start:[15,30],end:[16,15]}, {start:[19,20],end:[20,5]}],
            1: [{start:[14,0],end:[14,45]}],
            2: [{start:[11,30],end:[12,15]},{start:[17,0],end:[17,45]}],
            4: [{start:[18,10],end:[19,5]}],
            5: [{start:[11,30],end:[12,15]},{start:[17,0],end:[17,45]}]
        },

        get todayMidnight() {
            let d = new Date(); d.setHours(0,0,0,0); return d;
        },

        get maxDate() {
            let d = new Date(); d.setHours(0,0,0,0); d.setDate(d.getDate() + 13); return d;
        },

        getDayOfWeek(day) {
            let d = new Date(this.currentYear, this.currentMonth, day).getDay();
            return d === 0 ? 6 : d - 1;
        },

        isDisabled(day) {
            let dow = this.getDayOfWeek(day);
            if (dow === 3 || dow === 6) return true;
            let date = new Date(this.currentYear, this.currentMonth, day);
            return date < this.todayMidnight || date > this.maxDate;
        },

        pad(n) { return String(n).padStart(2, '0'); },

        dateKeyFor(day) {
            return this.currentYear + '-' + this.pad(this.currentMonth + 1) + '-' + this.pad(day);
        },

        isSlotBooked(dateKey, timeStr) {
            let booked = this.bookedSlotsByDate[dateKey] || [];
            return booked.includes(timeStr);
        },

        get convertedSlots() {
            if (this.selectedDay === null) return [];
            let dow = this.getDayOfWeek(this.selectedDay);
            let slots = this.slotsByDow[dow] || [];
            let dateKey = this.dateKeyFor(this.selectedDay);
            // Bookings must be made at least 24 hours in advance.
            let cutoff = new Date(Date.now() + 24 * 60 * 60 * 1000);
            return slots
                .map(slot => {
                    let startUtc = new Date(Date.UTC(this.currentYear, this.currentMonth, this.selectedDay, slot.start[0] - 1, slot.start[1]));
                    let endUtc   = new Date(Date.UTC(this.currentYear, this.currentMonth, this.selectedDay, slot.end[0]   - 1, slot.end[1]));
                    let fmt = t => t.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true, timeZone: this.userTz });
                    let time = this.pad(slot.start[0]) + ':' + this.pad(slot.start[1]) + ':00';
                    return {
                        time,
                        label: fmt(startUtc) + ' – ' + fmt(endUtc),
                        startUtc,
                        booked: this.isSlotBooked(dateKey, time),
                    };
                })
                .filter(s => s.startUtc > cutoff);
        },

        // Re-derives the raw HH:MM:SS time for the selected slot (needed for
        // the booking request — convertedSlots labels are display-only).
        get selectedSlotTime() {
            if (this.selectedDay === null || !this.selectedTime) return null;
            let match = this.convertedSlots.find(s => s.label === this.selectedTime);
            return match ? match.time : null;
        },

        async bookSlot() {
            if (this.selectedDay === null || !this.selectedTime || this.booking) return;
            this.booking = true;
            this.bookingError = '';
            let dateKey = this.dateKeyFor(this.selectedDay);
            let time = this.selectedSlotTime;
            try {
                const response = await fetch('{{ route('member.live-coaching.book') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ date: dateKey, time }),
                });
                const data = await response.json();
                if (!response.ok) {
                    this.bookingError = data.error || 'Something went wrong. Please try again.';
                    if (!this.bookedSlotsByDate[dateKey]) this.bookedSlotsByDate[dateKey] = [];
                    if (!this.bookedSlotsByDate[dateKey].includes(time)) this.bookedSlotsByDate[dateKey].push(time);
                    this.selectedTime = null;
                    return;
                }
                this.activeBooking = data.booking;
                this.sessionsUsed++;
                this.confirmed = true;
            } catch (e) {
                this.bookingError = 'Something went wrong. Please try again.';
            } finally {
                this.booking = false;
            }
        },

        // Re-derives the actual start/end Date objects for the selected slot
        // (convertedSlots only exposes formatted label strings for display).
        get selectedSlotRange() {
            if (this.activeBooking) {
                let parts = this.activeBooking.date.split('-');
                let timeParts = this.activeBooking.time.split(':');
                let yr = parseInt(parts[0]);
                let mo = parseInt(parts[1]) - 1;
                let dy = parseInt(parts[2]);
                let hr = parseInt(timeParts[0]);
                let mn = parseInt(timeParts[1]);

                let startUtc = new Date(Date.UTC(yr, mo, dy, hr - 1, mn));
                let endUtc   = new Date(Date.UTC(yr, mo, dy, hr - 1, mn + 45));
                return { startUtc, endUtc };
            }
            if (this.selectedDay === null || !this.selectedTime) return null;
            let dow = this.getDayOfWeek(this.selectedDay);
            let slots = this.slotsByDow[dow] || [];
            for (let slot of slots) {
                let startUtc = new Date(Date.UTC(this.currentYear, this.currentMonth, this.selectedDay, slot.start[0] - 1, slot.start[1]));
                let endUtc   = new Date(Date.UTC(this.currentYear, this.currentMonth, this.selectedDay, slot.end[0]   - 1, slot.end[1]));
                let fmt = t => t.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true, timeZone: this.userTz });
                if ((fmt(startUtc) + ' – ' + fmt(endUtc)) === this.selectedTime) {
                    return { startUtc, endUtc };
                }
            }
            return null;
        },

        icsDate(d) {
            return d.toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z';
        },

        get googleCalendarUrl() {
            let range = this.selectedSlotRange;
            if (!range) return '#';
            let title = encodeURIComponent('Live Piano Coaching Session with Kingsley Khord');
            let details = encodeURIComponent('Your one-on-one piano coaching session with Kingsley Khord.');
            let dates = this.icsDate(range.startUtc) + '/' + this.icsDate(range.endUtc);
            return `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${title}&dates=${dates}&details=${details}`;
        },

        get outlookCalendarUrl() {
            let range = this.selectedSlotRange;
            if (!range) return '#';
            let title = encodeURIComponent('Live Piano Coaching Session with Kingsley Khord');
            let details = encodeURIComponent('Your one-on-one piano coaching session with Kingsley Khord.');
            return `https://outlook.live.com/calendar/0/deeplink/compose?subject=${title}&startdt=${range.startUtc.toISOString()}&enddt=${range.endUtc.toISOString()}&body=${details}&path=/calendar/action/compose&rru=addevent`;
        },

        downloadIcs() {
            let range = this.selectedSlotRange;
            if (!range) return;
            let ics = [
                'BEGIN:VCALENDAR',
                'VERSION:2.0',
                'BEGIN:VEVENT',
                'UID:' + Date.now() + '@kingsleykhord.com',
                'DTSTAMP:' + this.icsDate(new Date()),
                'DTSTART:' + this.icsDate(range.startUtc),
                'DTEND:' + this.icsDate(range.endUtc),
                'SUMMARY:Live Piano Coaching Session with Kingsley Khord',
                'DESCRIPTION:Your one-on-one piano coaching session with Kingsley Khord.',
                'END:VEVENT',
                'END:VCALENDAR',
            ].join('\r\n');
            let blob = new Blob([ics], { type: 'text/calendar' });
            let url = URL.createObjectURL(blob);
            let a = document.createElement('a');
            a.href = url;
            a.download = 'piano-coaching-session.ics';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
            this.calendarMenuOpen = false;
        },

        get monthName() { return this.months[this.currentMonth]; },

        get firstDayOfMonth() {
            let d = new Date(this.currentYear, this.currentMonth, 1).getDay();
            return d === 0 ? 6 : d - 1;
        },

        get daysInMonth() { return new Date(this.currentYear, this.currentMonth + 1, 0).getDate(); },

        get selectedDateLabel() {
            if (this.selectedDay === null) return '';
            let d = new Date(this.currentYear, this.currentMonth, this.selectedDay);
            return d.toLocaleDateString('en-US', { weekday: 'short', month: 'long', day: 'numeric', year: 'numeric' });
        },

        isToday(day) {
            let t = new Date();
            return day === t.getDate() && this.currentMonth === t.getMonth() && this.currentYear === t.getFullYear();
        },

        get canGoPrev() {
            let now = new Date();
            return !(this.currentMonth === now.getMonth() && this.currentYear === now.getFullYear());
        },

        get canGoNext() {
            let next = new Date(this.currentYear, this.currentMonth + 1, 1);
            return next <= this.maxDate;
        },

        prevMonth() {
            if (!this.canGoPrev) return;
            if (this.currentMonth === 0) { this.currentMonth = 11; this.currentYear--; }
            else { this.currentMonth--; }
            this.selectedDay = null; this.selectedTime = null;
        },

        nextMonth() {
            if (!this.canGoNext) return;
            if (this.currentMonth === 11) { this.currentMonth = 0; this.currentYear++; }
            else { this.currentMonth++; }
            this.selectedDay = null; this.selectedTime = null;
        },

        selectDay(day) {
            if (this.isDisabled(day)) return;
            this.selectedDay = day; this.selectedTime = null;
        },

        getFormattedBookingDate() {
            if (!this.activeBooking) return '';
            let parts = this.activeBooking.date.split('-');
            let timeParts = this.activeBooking.time.split(':');
            let yr = parseInt(parts[0]);
            let mo = parseInt(parts[1]) - 1;
            let dy = parseInt(parts[2]);
            let hr = parseInt(timeParts[0]);
            let mn = parseInt(timeParts[1]);

            let startUtc = new Date(Date.UTC(yr, mo, dy, hr - 1, mn));
            return startUtc.toLocaleDateString('en-US', { weekday: 'short', month: 'long', day: 'numeric', year: 'numeric', timeZone: this.userTz });
        },

        getFormattedBookingTime() {
            if (!this.activeBooking) return '';
            let parts = this.activeBooking.date.split('-');
            let timeParts = this.activeBooking.time.split(':');
            let yr = parseInt(parts[0]);
            let mo = parseInt(parts[1]) - 1;
            let dy = parseInt(parts[2]);
            let hr = parseInt(timeParts[0]);
            let mn = parseInt(timeParts[1]);

            let startUtc = new Date(Date.UTC(yr, mo, dy, hr - 1, mn));
            let endUtc   = new Date(Date.UTC(yr, mo, dy, hr - 1, mn + 45));
            let fmt = t => t.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true, timeZone: this.userTz });
            return fmt(startUtc) + ' – ' + fmt(endUtc);
        },

        init() {
            try {
                const offset = -new Date().getTimezoneOffset();
                const h = Math.floor(Math.abs(offset) / 60);
                const m = Math.abs(offset) % 60;
                this.userTzLabel = 'GMT' + (offset >= 0 ? '+' : '-') + h + (m ? ':' + String(m).padStart(2,'0') : '');
            } catch(e) { this.userTzLabel = 'local time'; }
        }
    }"
    x-init="init()"
    >

    <div class="max-w-5xl mx-auto">

        <!-- ─── BOOKING FORM ─── -->
        <div x-show="!confirmed" x-transition>

            <a href="/home" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 mb-6 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Dashboard
            </a>

            <h1 class="text-2xl font-bold text-gray-900 mb-1">Book Your Live Coaching Session</h1>
            <p class="text-sm text-gray-500 mb-7">You get 1 free live coaching session every month as part of your membership.</p>

            <div x-show="sessionsUsed >= sessionsIncluded" x-cloak
                class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-5 py-4 mb-6 text-sm">
                You've used your free session for this month. Your next session becomes available on
                <span class="font-bold" x-text="nextResetLabel"></span>.
            </div>

            <!-- Stats Row -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-6">
                <div class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-y sm:divide-y-0 divide-gray-100">
                    <div class="flex items-center gap-4 px-6 py-5">
                        <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Monthly Session</p>
                            <p class="text-xl font-bold text-amber-500 leading-tight"><span x-text="sessionsUsed"></span> <span class="text-gray-300 font-light">/</span> <span x-text="sessionsIncluded"></span></p>
                            <p class="text-[11px] text-gray-400">Sessions used</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 px-6 py-5">
                        <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Duration</p>
                            <p class="text-base font-bold text-gray-800">45 Minutes</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 px-6 py-5">
                        <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Platform</p>
                            <p class="text-base font-bold text-gray-800">Zoom</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 px-6 py-5">
                        <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Next Reset</p>
                            <p class="text-base font-bold text-gray-800" x-text="nextResetLabel"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendar + Time Slots -->
            <div x-show="sessionsUsed < sessionsIncluded" x-cloak class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-100">

                    <!-- Left: Calendar -->
                    <div class="p-7">
                        <h2 class="text-base font-bold text-gray-900 mb-6">1. Select a Date</h2>
                        <div class="flex items-center justify-between mb-6">
                            <button @click="prevMonth()"
                                :class="canGoPrev ? 'text-gray-500 hover:bg-gray-100' : 'text-gray-200 cursor-not-allowed'"
                                class="p-1.5 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <span class="text-sm font-semibold text-gray-800" x-text="monthName + ' ' + currentYear"></span>
                            <button @click="nextMonth()"
                                :class="canGoNext ? 'text-gray-500 hover:bg-gray-100' : 'text-gray-200 cursor-not-allowed'"
                                class="p-1.5 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                        <div class="grid grid-cols-7 mb-2">
                            <template x-for="(d, i) in days" :key="d">
                                <div class="text-center text-xs font-semibold py-1"
                                    :class="(i === 3 || i === 6) ? 'text-red-300' : 'text-gray-400'"
                                    x-text="d"></div>
                            </template>
                        </div>
                        <div class="grid grid-cols-7 gap-y-1">
                            <template x-for="i in firstDayOfMonth" :key="'e'+i">
                                <div></div>
                            </template>
                            <template x-for="day in daysInMonth" :key="day">
                                <button
                                    @click="selectDay(day)"
                                    :disabled="isDisabled(day)"
                                    class="aspect-square flex items-center justify-center text-sm rounded-lg transition font-medium mx-auto w-9 h-9"
                                    :class="{
                                        'bg-amber-600 text-white font-bold shadow': selectedDay === day,
                                        'bg-amber-50 text-amber-700 font-semibold ring-1 ring-amber-300': isToday(day) && selectedDay !== day && !isDisabled(day),
                                        'text-gray-300 cursor-not-allowed': isDisabled(day),
                                        'text-gray-700 hover:bg-gray-100': !isDisabled(day) && !isToday(day) && selectedDay !== day
                                    }"
                                    x-text="day">
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Right: Time Slots -->
                    <div class="p-7 flex flex-col">
                        <h2 class="text-base font-bold text-gray-900 mb-1">2. Select a Time</h2>
                        <div class="flex items-center justify-between mb-6">
                            <p class="text-sm text-gray-400" x-text="selectedDay !== null ? 'Available slots for ' + selectedDateLabel : 'Select a date first'"></p>
                            <div class="flex items-center gap-1.5 text-xs text-gray-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                                </svg>
                                <span x-text="userTzLabel"></span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 flex-1" x-show="selectedDay !== null && convertedSlots.length > 0" x-transition>
                            <template x-for="slot in convertedSlots" :key="slot.time">
                                <label
                                    class="flex items-center gap-4 px-5 py-4 rounded-xl border transition-all duration-150"
                                    :class="slot.booked
                                        ? 'border-gray-100 bg-gray-50 cursor-not-allowed opacity-60'
                                        : (selectedTime === slot.label
                                            ? 'border-amber-500 bg-amber-50 ring-1 ring-amber-400 cursor-pointer'
                                            : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50 cursor-pointer')">
                                    <input type="radio" name="time" :value="slot.label" :disabled="slot.booked"
                                        @change="selectedTime = slot.label" :checked="selectedTime === slot.label" class="hidden">
                                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition"
                                        :class="slot.booked ? 'border-gray-300' : (selectedTime === slot.label ? 'border-amber-500' : 'border-gray-300')">
                                        <div class="w-2.5 h-2.5 rounded-full bg-amber-500 transition" x-show="!slot.booked && selectedTime === slot.label"></div>
                                    </div>
                                    <span class="text-sm font-semibold flex-1" :class="slot.booked ? 'text-gray-400' : 'text-gray-700'" x-text="slot.label"></span>
                                    <span x-show="slot.booked" class="text-[10px] font-bold text-gray-400 uppercase tracking-wide bg-gray-100 px-2 py-1 rounded-full">Booked</span>
                                </label>
                            </template>
                        </div>

                        <div x-show="selectedDay !== null && convertedSlots.length === 0" class="flex-1 flex items-center justify-center text-sm text-gray-400 italic">
                            No available slots for this day.
                        </div>

                        <div x-show="selectedDay === null" class="flex-1 flex items-center justify-center text-sm text-gray-400 italic">
                            Pick an available date to see time slots.
                        </div>

                        <p x-show="bookingError" x-text="bookingError" class="mt-3 text-sm text-red-600 font-medium"></p>

                        <button
                            @click="bookSlot()"
                            class="mt-6 w-full py-4 rounded-xl text-sm font-bold uppercase tracking-widest transition-all duration-200"
                            :class="selectedDay !== null && selectedTime !== null && !booking
                                ? 'bg-gray-900 text-white hover:bg-gray-700 shadow-md'
                                : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                            :disabled="selectedDay === null || selectedTime === null || booking">
                            <span x-text="booking ? 'Booking...' : 'Continue to Confirm'"></span>
                        </button>
                    </div>

                </div>
            </div>

        </div><!-- end booking form -->


        <!-- ─── CONFIRMATION VIEW ─── -->
        <div x-show="confirmed" x-transition>

            <nav class="flex items-center gap-1.5 text-xs text-gray-400 mb-3">
                <a href="/home" class="hover:text-gray-600">Dashboard</a>
                <span>/</span>
                <span class="text-gray-600 font-semibold">Live Session</span>
            </nav>

            <h1 class="text-2xl font-bold text-gray-900 mb-1">Your Live Coaching Session</h1>
            <p class="text-sm text-gray-500 mb-6">You get 1 free live coaching session every month as part of your membership.</p>

            <!-- Success Banner -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-6 px-6 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-base font-bold text-green-600">Your session is booked!</p>
                        <p class="text-sm text-gray-400">The Zoom link will be sent to your registered email.</p>
                    </div>
                </div>

                <!-- Add to Calendar -->
                <div class="relative flex-shrink-0" @click.away="calendarMenuOpen = false">
                    <button @click="calendarMenuOpen = !calendarMenuOpen"
                        class="flex items-center gap-2 text-sm font-semibold text-gray-700 border border-gray-200 px-4 py-2.5 rounded-xl hover:bg-gray-50 transition">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Add to Calendar
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="calendarMenuOpen" x-transition x-cloak
                        class="absolute right-0 mt-2 w-56 bg-white rounded-xl border border-gray-100 shadow-lg py-2 z-20">
                        <a :href="googleCalendarUrl" target="_blank" rel="noopener noreferrer"
                            @click="calendarMenuOpen = false"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                            Google Calendar
                        </a>
                        <a :href="outlookCalendarUrl" target="_blank" rel="noopener noreferrer"
                            @click="calendarMenuOpen = false"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                            Outlook Calendar
                        </a>
                        <button @click="downloadIcs()"
                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition text-left">
                            Apple / Other (.ics)
                        </button>
                    </div>
                </div>
            </div>

            <!-- Detail Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Left column -->
                <div class="flex flex-col gap-6">

                    <!-- Booked Session Card -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-7">
                        <p class="text-sm font-bold text-gray-900 mb-5">Your Booked Session</p>
                        <div class="flex items-start gap-4 mb-6">
                            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xl font-bold text-gray-900 leading-tight" x-text="activeBooking ? getFormattedBookingDate() : selectedDateLabel"></p>
                                <p class="text-sm text-gray-500 mt-0.5" x-text="(activeBooking ? getFormattedBookingTime() : selectedTime) + ' (45 Minutes)'"></p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-5 border-t border-gray-100">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                                </svg>
                                <div>
                                    <p class="text-[11px] text-gray-400">Platform</p>
                                    <p class="text-sm font-bold text-gray-800">Zoom</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                                </svg>
                                <div>
                                    <p class="text-[11px] text-gray-400">Time zone</p>
                                    <p class="text-sm font-bold text-gray-800" x-text="userTzLabel"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Important Notes Card -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-7">
                        <p class="text-sm font-bold text-gray-900 mb-5">Important Notes</p>
                        <ul class="flex flex-col gap-3 mb-6">
                            <li class="flex items-start gap-3">
                                <div class="w-5 h-5 rounded bg-green-500 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <span class="text-sm text-gray-600">You will receive an email with the Zoom link and session details.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="w-5 h-5 rounded bg-green-500 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <span class="text-sm text-gray-600">Please arrive a few minutes early to make the most of our time together.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="w-5 h-5 rounded bg-green-500 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <span class="text-sm text-gray-600">Come prepared with any questions or topics you'd like help with.</span>
                            </li>
                        </ul>
                    </div>

                </div>

                <!-- Right column -->
                <div class="flex flex-col gap-6">

                    <!-- Monthly Session Status -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-7">
                        <div class="flex items-center justify-between mb-6">
                            <p class="text-sm font-bold text-gray-900">Monthly Session Status</p>
                            <span class="text-xs font-bold text-green-700 bg-green-100 px-3 py-1.5 rounded-full" x-text="sessionsUsed + ' / ' + sessionsIncluded + ' Used'"></span>
                        </div>
                        <div class="flex flex-col gap-0 divide-y divide-gray-100">
                            <div class="flex items-center justify-between py-3.5">
                                <span class="text-sm text-gray-500">Sessions Included</span>
                                <span class="text-sm font-bold text-gray-900" x-text="sessionsIncluded"></span>
                            </div>
                            <div class="flex items-center justify-between py-3.5">
                                <span class="text-sm text-gray-500">Sessions Used</span>
                                <span class="text-sm font-bold text-gray-900" x-text="sessionsUsed"></span>
                            </div>
                            <div class="flex items-center justify-between py-3.5">
                                <span class="text-sm text-gray-500">Remaining Sessions</span>
                                <span class="text-sm font-bold text-gray-900" x-text="sessionsIncluded - sessionsUsed"></span>
                            </div>
                            <div class="flex items-center justify-between py-3.5">
                                <span class="text-sm text-gray-500">Next Available</span>
                                <span class="text-sm font-bold text-gray-900" x-text="nextResetLabel"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Watch Tutorial Card -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-7">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">Before Your Session</p>
                        <div class="flex items-start gap-4 mb-5">
                            <div class="w-11 h-11 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-base font-bold text-gray-900 leading-tight">Watch the Set-Up Tutorial</p>
                                <p class="text-sm text-gray-400 mt-1.5 leading-relaxed">Make sure you have the best experience. This short tutorial walks you through how to prepare for your live coaching session.</p>
                            </div>
                        </div>
                        <button class="w-full flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold px-5 py-3.5 rounded-xl transition shadow-sm shadow-indigo-200">
                            Watch Tutorial
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </button>
                    </div>

                </div>

            </div>
        </div><!-- end confirmation view -->

    </div>
</div>

@endsection
