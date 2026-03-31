<?php

// app/Filament/Resources/UserResource/RelationManagers/OrdersRelationManager.php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';
    protected static ?string $title = 'Історія замовлень';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->width(60),

                Tables\Columns\TextColumn::make('pickup_address')
                    ->label('Відправлення')
                    ->limit(25),

                Tables\Columns\TextColumn::make('delivery_address')
                    ->label('Доставка')
                    ->limit(25),

                Tables\Columns\TextColumn::make('vehicleType.name')
                    ->label('Транспорт')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Сума')
                    ->money('EUR'),

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
                    ->label('Дата')
                    ->date('d.m.Y'),
            ])

            ->defaultSort('created_at', 'desc')

            // Підсумок знизу: кількість і загальна сума
            ->paginated([10, 25, 50]);
    }
}

