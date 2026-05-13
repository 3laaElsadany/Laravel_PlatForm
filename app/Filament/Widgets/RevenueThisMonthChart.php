<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use App\Support\FilamentInstructor;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueThisMonthChart extends ChartWidget
{
    public ?string $filter = 'month';

    protected static ?int $sort = 3;

    protected ?string $heading = 'الإيرادات';

    protected ?string $maxHeight = '240px';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            'week' => 'أسبوع',
            'month' => 'شهر',
            'year' => 'سنة',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $now = Carbon::now()->locale(app()->getLocale());
        $instructorId = FilamentInstructor::instructorId();
        $labels = [];
        $data = [];

        if ($this->filter === 'year') {
            $start = $now->copy()->startOfYear();

            for ($month = 0; $month < 12; $month++) {
                $date = $start->copy()->addMonths($month);
                $labels[] = $date->translatedFormat('M');
                $data[] = $this->countRevenueByMonth($date, $instructorId);
            }
        } elseif ($this->filter === 'week') {
            $start = $now->copy()->startOfWeek(Carbon::MONDAY);

            for ($day = 0; $day < 7; $day++) {
                $date = $start->copy()->addDays($day);
                $labels[] = $date->translatedFormat('D');
                $data[] = $this->countRevenueByDay($date, $instructorId);
            }
        } else {
            $start = $now->copy()->startOfMonth();
            $end = $now->copy()->endOfMonth();

            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $labels[] = $date->format('j');
                $data[] = $this->countRevenueByDay($date, $instructorId);
            }
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'الإيرادات (USD)',
                    'data' => $data,
                    'backgroundColor' => 'rgba(245, 158, 11, 0.25)',
                    'borderColor' => 'rgb(217, 119, 6)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.25,
                ],
            ],
        ];
    }

    protected function countRevenueByDay(Carbon $date, ?int $instructorId): float
    {
        $query = Payment::query()
            ->where('status', 'completed')
            ->whereDate('paid_at', $date->toDateString());

        if ($instructorId !== null) {
            $query->whereHas('course', fn($cq) => $cq->where('instructor_id', $instructorId));
        }

        return round((float) $query->sum('amount'), 2);
    }

    protected function countRevenueByMonth(Carbon $date, ?int $instructorId): float
    {
        $query = Payment::query()
            ->where('status', 'completed')
            ->whereYear('paid_at', $date->year)
            ->whereMonth('paid_at', $date->month);

        if ($instructorId !== null) {
            $query->whereHas('course', fn($cq) => $cq->where('instructor_id', $instructorId));
        }

        return round((float) $query->sum('amount'), 2);
    }
}
