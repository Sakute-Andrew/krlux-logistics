<?php
// app/Filament/Resources/OrderResource/Pages/ListOrders.php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    // Кнопка "Створити замовлення" у хедері таблиці
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Нове замовлення'),
        ];
    }

    // Таби для швидкої фільтрації по статусу
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Всі')
                ->badge(fn () => \App\Models\Order::count()),

            'pending' => Tab::make('Очікують')
                ->badge(fn () => \App\Models\Order::where('status', 'pending')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending')),

            'in_progress' => Tab::make('В дорозі')
                ->badge(fn () => \App\Models\Order::where('status', 'in_progress')->count())
                ->badgeColor('primary')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'in_progress')),

            'completed' => Tab::make('Завершені')
                ->badge(fn () => \App\Models\Order::where('status', 'completed')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed')),

            'cancelled' => Tab::make('Скасовані')
                ->badge(fn () => \App\Models\Order::where('status', 'cancelled')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'cancelled')),
        ];
    }
}


