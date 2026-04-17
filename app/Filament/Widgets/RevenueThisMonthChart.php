<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueThisMonthChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'إيرادات الشهر الحالي (يوميًا)';

    protected ?string $maxHeight = '240px';

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $labels = [];
        $data = [];

        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
            $labels[] = $day->format('j');
            $sum = Payment::query()
                ->where('status', 'completed')
                ->whereDate('paid_at', $day->toDateString())
                ->sum('amount');
            $data[] = round((float) $sum, 2);
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
}
