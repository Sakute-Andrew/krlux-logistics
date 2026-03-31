<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\VehicleType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Замовлення';

    protected static ?string $modelLabel = 'Замовлення';

    protected static ?string $pluralModelLabel = 'Замовлення';

    protected static ?int $navigationSort = 1;

    // -------------------------------------------------------------------------
    // FORM  (використовується для Create і Edit)
    // -------------------------------------------------------------------------

    public static function form(Form $form): Form
    {
        return $form->schema([

            // --- Клієнт ---
            Forms\Components\Section::make('Дані клієнта')
                ->icon('heroicon-o-user')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('customer_name')
                        ->label('Ім\'я клієнта')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('phone')
                        ->label('Телефон')
                        ->tel()
                        ->required()
                        ->maxLength(20),

                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(255),

                    Forms\Components\Select::make('user_id')
                        ->label('Прив\'язаний акаунт')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->placeholder('— не прив\'язано —'),
                ]),

            // --- Маршрут та авто ---
            Forms\Components\Section::make('Маршрут та транспорт')
                ->icon('heroicon-o-map-pin')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('vehicle_type_id')
                        ->label('Тип транспорту')
                        ->options(fn () => VehicleType::pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->preload(),

                    Forms\Components\TextInput::make('distance_km')
                        ->label('Відстань (км)')
                        ->numeric()
                        ->minValue(0)
                        ->suffix('км'),

                    Forms\Components\Textarea::make('pickup_address')
                        ->label('Адреса відправлення')
                        ->required()
                        ->rows(2)
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('delivery_address')
                        ->label('Адреса доставки')
                        ->required()
                        ->rows(2)
                        ->columnSpanFull(),
                ]),

            // --- Деталі замовлення ---
            Forms\Components\Section::make('Деталі замовлення')
                ->icon('heroicon-o-banknotes')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('total_price')
                        ->label('Вартість')
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->prefix('₴'),

                    Forms\Components\DateTimePicker::make('scheduled_at')
                        ->label('Запланований час')
                        ->seconds(false)
                        ->nullable(),

                    Forms\Components\Select::make('status')
                        ->label('Статус')
                        ->options([
                            'pending'     => 'Очікує',
                            'in_progress' => 'В дорозі',
                            'completed'   => 'Завершено',
                            'cancelled'   => 'Скасовано',
                        ])
                        ->required()
                        ->default('pending')
                        ->native(false),

                    Forms\Components\Select::make('driver_id')
                        ->label('Водій')
                        ->options(fn () => \App\Models\Driver::where('status', 'available')
                            ->get()
                            ->mapWithKeys(fn ($d) => [$d->id => "{$d->name} — {$d->phone}"])
                        )
                        ->searchable()
                        ->nullable()
                        ->placeholder('— не призначено —'),
                ]),

            // --- Нотатки ---
            Forms\Components\Section::make('Нотатки')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->columns(2)
                ->collapsed()
                ->schema([
                    Forms\Components\Textarea::make('customer_note')
                        ->label('Нотатка клієнта')
                        ->rows(3),

                    Forms\Components\Textarea::make('admin_note')
                        ->label('Нотатка адміна')
                        ->rows(3),
                ]),
        ]);
    }

    // -------------------------------------------------------------------------
    // TABLE  (список замовлень)
    // -------------------------------------------------------------------------

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->width(60),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Клієнт')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable(),

                Tables\Columns\TextColumn::make('vehicleType.name')
                    ->label('Транспорт')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('pickup_address')
                    ->label('Відправлення')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->pickup_address),

                Tables\Columns\TextColumn::make('delivery_address')
                    ->label('Доставка')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->delivery_address),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Вартість')
                    ->money('UAH')
                    ->sortable(),

                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Заплановано')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->placeholder('—'),

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
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->defaultSort('created_at', 'desc')

            // --- Фільтри ---
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'pending'     => 'Очікує',
                        'in_progress' => 'В дорозі',
                        'completed'   => 'Завершено',
                        'cancelled'   => 'Скасовано',
                    ]),

                Tables\Filters\SelectFilter::make('vehicle_type_id')
                    ->label('Тип транспорту')
                    ->options(fn () => VehicleType::pluck('name', 'id')),

                Tables\Filters\Filter::make('scheduled_at')
                    ->label('Є запланований час')
                    ->query(fn (Builder $query) => $query->whereNotNull('scheduled_at')),
            ])

            // --- Дії в рядку ---
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

                // Швидка зміна статусу прямо з таблиці
                Tables\Actions\Action::make('markCompleted')
                    ->label('Завершити')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Order $record) => $record->status === 'in_progress')
                    ->requiresConfirmation()
                    ->action(fn (Order $record) => $record->update(['status' => 'completed'])),

                Tables\Actions\Action::make('markInProgress')
                    ->label('В дорогу')
                    ->icon('heroicon-o-truck')
                    ->color('primary')
                    ->visible(fn (Order $record) => $record->status === 'pending')
                    ->action(fn (Order $record) => $record->update(['status' => 'in_progress'])),
            ])

            // --- Масові дії ---
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('bulkCancel')
                        ->label('Скасувати вибрані')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['status' => 'cancelled'])),
                ]),
            ]);
    }

    // -------------------------------------------------------------------------
    // INFOLIST  (сторінка перегляду View)
    // -------------------------------------------------------------------------

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            Infolists\Components\Section::make('Дані клієнта')
                ->icon('heroicon-o-user')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('customer_name')
                        ->label('Ім\'я'),

                    Infolists\Components\TextEntry::make('phone')
                        ->label('Телефон'),

                    Infolists\Components\TextEntry::make('email')
                        ->label('Email')
                        ->placeholder('—'),

                    Infolists\Components\TextEntry::make('user.name')
                        ->label('Акаунт')
                        ->placeholder('не прив\'язано'),
                ]),

            Infolists\Components\Section::make('Маршрут та транспорт')
                ->icon('heroicon-o-map-pin')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('vehicleType.name')
                        ->label('Тип транспорту')
                        ->badge()
                        ->color('info'),

                    Infolists\Components\TextEntry::make('distance_km')
                        ->label('Відстань')
                        ->suffix(' км')
                        ->placeholder('—'),

                    Infolists\Components\TextEntry::make('pickup_address')
                        ->label('Адреса відправлення')
                        ->columnSpanFull(),

                    Infolists\Components\TextEntry::make('delivery_address')
                        ->label('Адреса доставки')
                        ->columnSpanFull(),
                ]),

            Infolists\Components\Section::make('Деталі замовлення')
                ->icon('heroicon-o-banknotes')
                ->columns(3)
                ->schema([
                    Infolists\Components\TextEntry::make('total_price')
                        ->label('Вартість')
                        ->money('UAH'),

                    Infolists\Components\TextEntry::make('scheduled_at')
                        ->label('Заплановано')
                        ->dateTime('d.m.Y H:i')
                        ->placeholder('—'),

                    Infolists\Components\TextEntry::make('status')
                        ->label('Статус')
                        ->badge()
                        ->color(fn (string $state) => match ($state) {
                            'pending'     => 'warning',
                            'in_progress' => 'primary',
                            'completed'   => 'success',
                            'cancelled'   => 'danger',
                            default       => 'gray',
                        })
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'pending'     => 'Очікує',
                            'in_progress' => 'В дорозі',
                            'completed'   => 'Завершено',
                            'cancelled'   => 'Скасовано',
                            default       => $state,
                        }),
                ]),

            Infolists\Components\Section::make('Нотатки')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('customer_note')
                        ->label('Нотатка клієнта')
                        ->placeholder('—'),

                    Infolists\Components\TextEntry::make('admin_note')
                        ->label('Нотатка адміна')
                        ->placeholder('—'),
                ]),

            Infolists\Components\Section::make('Системна інформація')
                ->columns(2)
                ->collapsed()
                ->schema([
                    Infolists\Components\TextEntry::make('created_at')
                        ->label('Створено')
                        ->dateTime('d.m.Y H:i'),

                    Infolists\Components\TextEntry::make('updated_at')
                        ->label('Оновлено')
                        ->dateTime('d.m.Y H:i'),
                ]),
        ]);
    }

    // -------------------------------------------------------------------------
    // PAGES
    // -------------------------------------------------------------------------

    public static function getRelationManagers(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view'   => Pages\ViewOrder::route('/{record}'),
            'edit'   => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
