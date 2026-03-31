<?php

// app/Filament/Widgets/StatsOverview.php

namespace App\Filament\Widgets;

use App\Models\Driver;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $todayOrders    = Order::whereDate('created_at', today())->count();
        $pendingOrders  = Order::where('status', 'pending')->count();
        $activeOrders   = Order::where('status', 'in_progress')->count();
        $driversOnRoad  = Driver::where('status', 'busy')->count();
        $driversAvail   = Driver::where('status', 'available')->count();

        $revenueToday   = Order::whereDate('created_at', today())
            ->where('status', 'completed')
            ->sum('total_price');

        $revenueMonth   = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'completed')
            ->sum('total_price');

        $completedTotal = Order::where('status', 'completed')->count();
        $cancelledTotal = Order::where('status', 'cancelled')->count();
        $totalOrders    = Order::count();
        $successRate    = $totalOrders > 0
            ? round($completedTotal / $totalOrders * 100)
            : 0;

        return [
            Stat::make('Замовлень сьогодні', $todayOrders)
                ->description('Нових за сьогодні')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary'),

            Stat::make('Очікують підтвердження', $pendingOrders)
                ->description('Потребують уваги')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingOrders > 0 ? 'warning' : 'success'),

            Stat::make('В дорозі', $activeOrders)
                ->description("Водіїв зайнято: {$driversOnRoad} / Вільних: {$driversAvail}")
                ->descriptionIcon('heroicon-m-truck')
                ->color('info'),

            Stat::make('Виручка сьогодні', '€' . number_format($revenueToday, 2))
                ->description('За завершеними замовленнями')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Виручка за місяць', '€' . number_format($revenueMonth, 2))
                ->description(now()->format('F Y'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success'),

            Stat::make('Успішність', $successRate . '%')
                ->description("Завершено: {$completedTotal} / Скасовано: {$cancelledTotal}")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($successRate >= 80 ? 'success' : 'warning'),
        ];
    }
}
