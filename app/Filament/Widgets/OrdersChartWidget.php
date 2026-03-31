<?php

// app/Filament/Widgets/OrdersChartWidget.php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class OrdersChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Замовлення за останні 14 днів';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $days   = collect(range(13, 0))->map(fn ($i) => now()->subDays($i)->format('Y-m-d'));
        $labels = $days->map(fn ($d) => Carbon::parse($d)->format('d.m'));

        $orders = Order::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [now()->subDays(13)->startOfDay(), now()->endOfDay()])
            ->groupBy('date')
            ->pluck('count', 'date');

        $revenue = Order::selectRaw('DATE(created_at) as date, SUM(total_price) as total')
            ->where('status', 'completed')
            ->whereBetween('created_at', [now()->subDays(13)->startOfDay(), now()->endOfDay()])
            ->groupBy('date')
            ->pluck('total', 'date');

        return [
            'datasets' => [
                [
                    'label'           => 'Замовлення',
                    'data'            => $days->map(fn ($d) => $orders[$d] ?? 0)->values()->toArray(),
                    'borderColor'     => '#A68966',
                    'backgroundColor' => 'rgba(166, 137, 102, 0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                    'yAxisID'         => 'y',
                ],
                [
                    'label'           => 'Виручка (€)',
                    'data'            => $days->map(fn ($d) => round($revenue[$d] ?? 0, 2))->values()->toArray(),
                    'borderColor'     => '#4A90A4',
                    'backgroundColor' => 'rgba(74, 144, 164, 0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                    'yAxisID'         => 'y1',
                ],
            ],
            'labels' => $labels->values()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y'  => ['position' => 'left',  'beginAtZero' => true, 'title' => ['display' => true, 'text' => 'Замовлення']],
                'y1' => ['position' => 'right', 'beginAtZero' => true, 'title' => ['display' => true, 'text' => 'Виручка (€)'], 'grid' => ['drawOnChartArea' => false]],
            ],
        ];
    }
}
