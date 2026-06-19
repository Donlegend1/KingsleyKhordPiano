<?php

namespace App\Console\Commands;

use App\Models\BookingAvailability;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateBookingAvailability extends Command
{
    protected $signature = 'booking:generate-availability {--days=14}';
    protected $description = 'Generate guest booking availability slots for a rolling booking window based on the weekly recurring schedule';

    /**
     * Carbon::dayOfWeek => list of start times (24h "H:i:s").
     * Wednesday (3) and Sunday (0) are intentionally omitted (closed).
     */
    private const SCHEDULE = [
        Carbon::MONDAY => ['12:30:00', '21:00:00'],
        Carbon::TUESDAY => ['10:00:00', '16:00:00', '19:00:00'],
        Carbon::THURSDAY => ['11:30:00', '14:00:00', '17:00:00'],
        Carbon::FRIDAY => ['10:00:00', '16:00:00'],
        Carbon::SATURDAY => ['14:00:00', '18:00:00'],
    ];

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $created = 0;

        $windowStart = Carbon::today();
        $windowEnd = Carbon::today()->addDays($days - 1);

        $date = $windowStart->copy();
        for ($i = 0; $i < $days; $i++, $date->addDay()) {
            $times = self::SCHEDULE[$date->dayOfWeek] ?? [];

            foreach ($times as $time) {
                $availability = BookingAvailability::firstOrCreate([
                    'date' => $date->toDateString(),
                    'time' => $time,
                ]);

                if ($availability->wasRecentlyCreated) {
                    $created++;
                }
            }
        }

        // Keep the booking window capped: drop unbooked slots outside the
        // current window (past dates, or beyond the 2-week horizon).
        $removed = BookingAvailability::where('is_booked', false)
            ->where(function ($query) use ($windowStart, $windowEnd) {
                $query->where('date', '<', $windowStart->toDateString())
                    ->orWhere('date', '>', $windowEnd->toDateString());
            })
            ->delete();

        // Drop unbooked slots inside the window that no longer match the
        // current weekly schedule (e.g. a time was removed/changed).
        $stale = BookingAvailability::where('is_booked', false)
            ->whereBetween('date', [$windowStart->toDateString(), $windowEnd->toDateString()])
            ->get()
            ->filter(function ($slot) {
                $times = self::SCHEDULE[Carbon::parse($slot->date)->dayOfWeek] ?? [];
                return !in_array($slot->time, $times, true);
            });

        $staleCount = $stale->count();
        BookingAvailability::whereIn('id', $stale->pluck('id'))->delete();
        $removed += $staleCount;

        $this->info("Generated {$created} new slot(s) and removed {$removed} stale/out-of-window slot(s). Booking window: {$windowStart->toDateString()} to {$windowEnd->toDateString()}.");

        return Command::SUCCESS;
    }
}
