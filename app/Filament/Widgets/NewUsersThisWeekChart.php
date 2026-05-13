<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class NewUsersThisWeekChart extends ChartWidget
{
    public ?string $filter = 'week';

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected static ?int $sort = 2;

    protected ?string $heading = 'تسجيلات المدرّسين والطلبة';

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

        $labels = [];
        $teacherData = [];
        $studentData = [];

        if ($this->filter === 'month') {
            $start = $now->copy()->startOfMonth();
            $daysInMonth = $start->daysInMonth;

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = $start->copy()->addDays($day - 1);
                $labels[] = $date->format('j');
                $teacherData[] = $this->countUsersByDay('teacher', $date);
                $studentData[] = $this->countUsersByDay('student', $date);
            }
        } elseif ($this->filter === 'year') {
            $start = $now->copy()->startOfYear();

            for ($month = 0; $month < 12; $month++) {
                $date = $start->copy()->addMonths($month);
                $labels[] = $date->translatedFormat('M');
                $teacherData[] = $this->countUsersByMonth('teacher', $date);
                $studentData[] = $this->countUsersByMonth('student', $date);
            }
        } else {
            $start = $now->copy()->startOfWeek(Carbon::MONDAY);

            for ($day = 0; $day < 7; $day++) {
                $date = $start->copy()->addDays($day);
                $labels[] = $date->translatedFormat('D');
                $teacherData[] = $this->countUsersByDay('teacher', $date);
                $studentData[] = $this->countUsersByDay('student', $date);
            }
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'مدرّسون جدد',
                    'data' => $teacherData,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.25)',
                    'borderColor' => 'rgb(16, 185, 129)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.25,
                ],
                [
                    'label' => 'طلبة جدد',
                    'data' => $studentData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.25)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.25,
                ],
            ],
        ];
    }

    protected function countUsersByDay(string $role, Carbon $date): int
    {
        return User::query()
            ->where('role', $role)
            ->whereDate('created_at', $date->toDateString())
            ->count();
    }

    protected function countUsersByMonth(string $role, Carbon $date): int
    {
        return User::query()
            ->where('role', $role)
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->count();
    }
}
