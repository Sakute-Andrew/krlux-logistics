<?php

// app/Filament/Resources/DriverResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\DriverResource\Pages;
use App\Models\Driver;
use App\Models\VehicleType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DriverResource extends Resource
{
    protected static ?string $model = Driver::class;

    protected static ?string $navigationIcon  = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Водії';
    protected static ?string $modelLabel      = 'Водій';
    protected static ?string $pluralModelLabel = 'Водії';
    protected static ?int    $navigationSort  = 2;

    // -------------------------------------------------------------------------
    // FORM
    // -------------------------------------------------------------------------

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Особисті дані')
                ->icon('heroicon-o-user')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label("Ім'я")
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('phone')
                        ->label('Телефон')
                        ->tel()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(20),

                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->unique(ignoreRecord: true)
                        ->nullable(),

                    Forms\Components\Select::make('vehicle_type_id')
                        ->label('Тип транспорту')
                        ->options(fn () => VehicleType::where('is_active', true)->pluck('name', 'id'))
                        ->searchable()
                        ->nullable()
                        ->placeholder('— не призначено —'),
                ]),

            Forms\Components\Section::make('Статус та нотатки')
                ->icon('heroicon-o-signal')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('status')
                        ->label('Статус')
                        ->options([
                            'available'   => 'Вільний',
                            'busy'        => 'Зайнятий',
                            'unavailable' => 'Недоступний',
                        ])
                        ->required()
                        ->default('available')
                        ->native(false),

                    Forms\Components\Textarea::make('note')
                        ->label('Нотатка')
                        ->rows(3)
                        ->nullable(),
                ]),
        ]);
    }

    // -------------------------------------------------------------------------
    // TABLE
    // -------------------------------------------------------------------------

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label("Ім'я")
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable(),

                Tables\Columns\TextColumn::make('vehicleType.name')
                    ->label('Транспорт')
                    ->badge()
                    ->color('info')
                    ->placeholder('—'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Статус')
                    ->colors([
                        'success' => 'available',
                        'warning' => 'busy',
                        'danger'  => 'unavailable',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'available'   => 'Вільний',
                        'busy'        => 'Зайнятий',
                        'unavailable' => 'Недоступний',
                        default       => $state,
                    }),

                Tables\Columns\TextColumn::make('orders_count')
                    ->label('Замовлень')
                    ->counts('orders')
                    ->sortable(),
            ])

            ->defaultSort('name')

            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'available'   => 'Вільний',
                        'busy'        => 'Зайнятий',
                        'unavailable' => 'Недоступний',
                    ]),

                Tables\Filters\SelectFilter::make('vehicle_type_id')
                    ->label('Тип транспорту')
                    ->options(fn () => VehicleType::pluck('name', 'id')),
            ])

            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                // Швидка зміна статусу
                Tables\Actions\Action::make('setAvailable')
                    ->label('Вільний')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Driver $record) => $record->status !== 'available')
                    ->action(fn (Driver $record) => $record->update(['status' => 'available'])),

                Tables\Actions\Action::make('setUnavailable')
                    ->label('Недоступний')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(fn (Driver $record) => $record->status !== 'unavailable')
                    ->action(fn (Driver $record) => $record->update(['status' => 'unavailable'])),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // -------------------------------------------------------------------------
    // INFOLIST
    // -------------------------------------------------------------------------

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Особисті дані')
                ->icon('heroicon-o-user')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('name')->label("Ім'я"),
                    Infolists\Components\TextEntry::make('phone')->label('Телефон'),
                    Infolists\Components\TextEntry::make('email')->label('Email')->placeholder('—'),
                    Infolists\Components\TextEntry::make('vehicleType.name')
                        ->label('Транспорт')
                        ->badge()
                        ->color('info')
                        ->placeholder('не призначено'),
                ]),

            Infolists\Components\Section::make('Статус')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('status')
                        ->label('Статус')
                        ->badge()
                        ->color(fn (string $state) => match ($state) {
                            'available'   => 'success',
                            'busy'        => 'warning',
                            'unavailable' => 'danger',
                            default       => 'gray',
                        })
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'available'   => 'Вільний',
                            'busy'        => 'Зайнятий',
                            'unavailable' => 'Недоступний',
                            default       => $state,
                        }),

                    Infolists\Components\TextEntry::make('note')->label('Нотатка')->placeholder('—'),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDrivers::route('/'),
            'create' => Pages\CreateDriver::route('/create'),
            'view'   => Pages\ViewDriver::route('/{record}'),
            'edit'   => Pages\EditDriver::route('/{record}/edit'),
        ];
    }
}
