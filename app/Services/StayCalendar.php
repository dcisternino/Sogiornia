<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;

/** Distribuisce le notti prenotate sui giorni locali, indipendentemente dallo stato del link ospite. */
class StayCalendar
{
    /** @return array<string, mixed> */
    public function forMonth(Builder $query, string $month): array
    {
        $first = CarbonImmutable::createFromFormat('!Y-m', $month, 'Europe/Rome');
        $start = $first->startOfWeek(CarbonInterface::MONDAY);
        $end = $first->endOfMonth()->startOfDay()->endOfWeek(CarbonInterface::SUNDAY)->startOfDay()->addDay();
        $days = [];
        for ($day = $start; $day->lessThan($end); $day = $day->addDay()) {
            $days[$day->format('Y-m-d')] = ['date' => $day, 'inMonth' => $day->month === $first->month, 'today' => $day->isSameDay(CarbonImmutable::now('Europe/Rome')), 'stays' => [], 'occupiedRooms' => 0];
        }
        $stays = (clone $query)->where('starts_at', '<', $end->utc())->where('ends_at', '>', $start->utc())
            ->with('room.property')->orderBy('starts_at')->orderBy('id')->get();
        foreach ($stays as $stay) {
            $arrival = $stay->starts_at->timezone('Europe/Rome')->startOfDay();
            $departure = $stay->ends_at->timezone('Europe/Rome')->startOfDay();
            if ($arrival->equalTo($departure)) {
                $departure = $departure->addDay();
            }
            $from = $arrival->greaterThan($start) ? $arrival : $start;
            $until = $departure->lessThan($end) ? $departure : $end;
            for ($day = $from; $day->lessThan($until); $day = $day->addDay()) {
                $days[$day->format('Y-m-d')]['stays'][] = $stay;
            }
        }
        foreach ($days as &$day) {
            $day['occupiedRooms'] = collect($day['stays'])->pluck('room_id')->unique()->count();
        }
        unset($day);

        return ['label' => $first->locale('it')->translatedFormat('F Y'), 'days' => array_values($days),
            'previous' => $first->year === 1900 && $first->month === 1 ? null : $first->subMonth()->format('Y-m'),
            'next' => $first->year === 2199 && $first->month === 12 ? null : $first->addMonth()->format('Y-m')];
    }
}
