<?php
// app/Filament/Resources/OrderResource/Pages/ViewOrder.php

namespace App\Filament\Resources\OrderResource\Pages;

use Filament\Forms;
use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;


class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            // Швидкі дії зміни статусу прямо на сторінці перегляду
            Actions\Action::make('markInProgress')
                ->label('В дорогу')
                ->icon('heroicon-o-truck')
                ->color('primary')
                ->visible(fn (Order $record) => $record->status === 'pending')
                ->action(function (Order $record) {
                    $record->update(['status' => 'in_progress']);
                    $this->refreshFormData(['status']);
                }),

            Actions\Action::make('markCompleted')
                ->label('Завершити')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (Order $record) => $record->status === 'in_progress')
                ->requiresConfirmation()
                ->action(function (Order $record) {
                    $record->update(['status' => 'completed']);
                    $this->refreshFormData(['status']);
                }),

            Actions\Action::make('markCancelled')
                ->label('Скасувати')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn (Order $record) => in_array($record->status, ['pending', 'in_progress']))
                ->requiresConfirmation()
                ->modalHeading('Скасувати замовлення?')
                ->modalDescription('Цю дію не можна буде відмінити.')
                ->action(function (Order $record) {
                    $record->update(['status' => 'cancelled']);
                    $this->refreshFormData(['status']);
                }),

            Actions\DeleteAction::make(),

            Actions\Action::make('assignDriver')
                ->label('Призначити водія')
                ->icon('heroicon-o-user-plus')
                ->color('primary')
                ->visible(fn (Order $record) => ! $record->driver_id)
                ->form([
                    Forms\Components\Select::make('driver_id')
                        ->label('Водій')
                        ->options(fn () => \App\Models\Driver::where('status', 'available')
                            ->get()
                            ->mapWithKeys(fn ($d) => [$d->id => "{$d->name} — {$d->phone}"])
                        )
                        ->required()
                        ->searchable(),
                ])
                ->action(function (Order $record, array $data) {
                    // Призначаємо водія
                    $record->update([
                        'driver_id' => $data['driver_id'],
                        'status'    => 'in_progress',
                    ]);

                    // Змінюємо статус водія на "зайнятий"
                    \App\Models\Driver::find($data['driver_id'])
                        ->update(['status' => 'busy']);
                }),
        ];
    }
}




