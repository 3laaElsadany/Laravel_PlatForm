<?php

namespace App\Filament\Widgets;

use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlatformOverviewStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'نظرة عامة';

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $users = User::query()->count();
        $purchases = Enrollment::query()->count();
        $revenue = (float) Payment::query()
            ->where('status', 'completed')
            ->sum('amount');

        return [
            Stat::make('إجمالي المستخدمين', number_format($users))
                ->description('كل الحسابات')
                ->icon(Heroicon::OutlinedUsers),
            Stat::make('عمليات الشراء', number_format($purchases))
                ->description('اشتراكات مسجّلة')
                ->icon(Heroicon::OutlinedShoppingCart),
            Stat::make('إجمالي الإيرادات', number_format($revenue, 2).' USD')
                ->description('دفعات مكتملة')
                ->icon(Heroicon::OutlinedBanknotes),
        ];
    }
}
