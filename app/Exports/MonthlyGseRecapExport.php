<?php

namespace App\Exports;

use App\Models\GseRecap;
use App\Support\GseDuration;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MonthlyGseRecapExport implements FromView, ShouldAutoSize
{
    public function __construct(
        private string $month,
        private int $gseType,
        private ?string $branch = null,
        private ?string $airline = null,
    ) {}

    public static function durationSeconds(?string $start, ?string $finish): ?int
    {
        return GseDuration::seconds($start, $finish);
    }

    public function view(): View
    {
        $start = CarbonImmutable::createFromFormat('!Y-m', $this->month, 'Asia/Jakarta');
        $recaps = GseRecap::query()
            ->with(['equipment.aircraft', 'gpuDetail', 'pushbackDetail'])
            ->where('gse_type_id', $this->gseType)
            ->whereBetween('service_date', [$start->toDateString(), $start->endOfMonth()->toDateString()])
            ->when($this->branch, fn ($query) => $query->where('branch_id', $this->branch))
            ->when($this->airline, fn ($query) => $query->where('airline_id', $this->airline))
            ->orderBy('service_date')
            ->orderBy('er_number')
            ->orderBy('id')
            ->get();

        $rows = $recaps->map(function ($recap) {
            $start = $recap->gpuDetail?->start_time ?? $recap->pushbackDetail?->start_ps;
            $finish = $recap->gpuDetail?->end_time ?? $recap->pushbackDetail?->end_ps;

            return [
                'recap' => $recap,
                'start' => GseDuration::formatTime($start) ?? $start,
                'finish' => GseDuration::formatTime($finish) ?? $finish,
                'duration' => self::durationSeconds($start, $finish),
            ];
        });

        $days = [];
        for ($day = $start; $day->month === $start->month; $day = $day->addDay()) {
            $days[] = $day;
        }

        return view('exports.monthly-gse-recap', [
            'days' => $days,
            'rowsByDate' => $rows->groupBy(fn ($row) => $row['recap']->service_date->toDateString()),
            'totalDuration' => $rows->sum('duration'),
        ]);
    }
}
