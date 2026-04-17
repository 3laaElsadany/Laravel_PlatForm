<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class NewUsersThisWeekChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'تسجيلات المستخدمين هذا الأسبوع';

    protected ?string $maxHeight = '240px';

    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $start = Carbon::now()->locale('ar')->startOfWeek(Carbon::MONDAY);
        $labels = [];
        $data = [];

        for ($i = 0; $i < 7; $i++) {
            $day = $start->copy()->addDays($i);
            $labels[] = $day->translatedFormat('D');
            $data[] = User::query()->whereDate('created_at', $day->toDateString())->count();
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'مستخدمون جدد',
                    'data' => $data,
                    'backgroundColor' => 'rgba(99, 102, 241, 0.45)',
                    'borderColor' => 'rgb(79, 70, 229)',
                    'borderWidth' => 1,
                ],
            ],
        ];
    }
}
