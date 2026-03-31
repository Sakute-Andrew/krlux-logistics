<?php

namespace App\Filament\Resources;

use App\Filament\Widgets\LatestOrdersWidget;
use App\Filament\Widgets\OrdersChartWidget;
use App\Filament\Widgets\StatsOverview;

class Dashboard
{
    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
            OrdersChartWidget::class,
            LatestOrdersWidget::class,
        ];
    }
}
