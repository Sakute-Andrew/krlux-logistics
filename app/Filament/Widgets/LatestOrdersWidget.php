<?php

// app/Filament/Widgets/LatestOrdersWidget.php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrdersWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Останні замовлення';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->with(['vehicleType', 'driver'])
                    ->latest()
                    ->limit(8)
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->width(60),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Клієнт')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Телефон'),

                Tables\Columns\TextColumn::make('vehicleType.name')
                    ->label('Транспорт')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('pickup_address')
                    ->label('Звідки')
                    ->limit(25),

                Tables\Columns\TextColumn::make('delivery_address')
                    ->label('Куди')
                    ->limit(25),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Сума')
                    ->money('EUR'),

                Tables\Columns\TextColumn::make('driver.name')
                    ->label('Водій')
                    ->placeholder('— не призначено —'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Статус')
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'in_progress',
                        'success' => 'completed',
                        'danger'  => 'cancelled',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending'     => 'Очікує',
                        'in_progress' => 'В дорозі',
                        'completed'   => 'Завершено',
                        'cancelled'   => 'Скасовано',
                        default       => $state,
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime('d.m H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Переглянути')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Order $record) => route('filament.admin.resources.orders.view', $record)),
            ]);
    }
}
