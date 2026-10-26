<?php

namespace App\Exports;

use App\Models\GseRecap;
use Carbon\CarbonImmutable;
use DateTimeImmutable;
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
        $start = self::parseTime($start);
        $finish = self::parseTime($finish);
        if ($start === null || $finish === null) {
            return null;
        }

        // An earlier finish belongs to the next day; equal times mean zero duration.
        if ($finish < $start) {
            $finish = $finish->modify('+1 day');
        }

        return $finish->getTimestamp() - $start->getTimestamp();
    }

    private static function parseTime(?string $value): ?DateTimeImmutable
    {
        $value = str_replace('.', ':', trim($value ?? ''));
        if (! preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $value)) {
            return null;
        }

        $format = substr_count($value, ':') === 2 ? '!G:i:s' : '!G:i';
        $time = DateTimeImmutable::createFromFormat($format, $value, new \DateTimeZone('Asia/Jakarta'));
        $errors = DateTimeImmutable::getLastErrors();

        return $time !== false && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))
            ? $time : null;
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
                'start' => self::parseTime($start)?->format('H:i') ?? $start,
                'finish' => self::parseTime($finish)?->format('H:i') ?? $finish,
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
