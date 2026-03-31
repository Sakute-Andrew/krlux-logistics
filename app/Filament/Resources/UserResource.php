<?php

// app/Filament/Resources/UserResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon   = 'heroicon-o-users';
    protected static ?string $navigationLabel  = 'Клієнти';
    protected static ?string $modelLabel       = 'Клієнт';
    protected static ?string $pluralModelLabel = 'Клієнти';
    protected static ?int    $navigationSort   = 3;

    // -------------------------------------------------------------------------
    // FORM
    // -------------------------------------------------------------------------

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Дані клієнта')
                ->icon('heroicon-o-user')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label("Ім'я")
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Forms\Components\TextInput::make('password')
                        ->label('Пароль')
                        ->password()
                        ->revealable()
                        ->dehydrated(fn ($state) => filled($state)) // зберігати тільки якщо заповнено
                        ->required(fn (string $operation) => $operation === 'create')
                        ->helperText('Залиш порожнім щоб не змінювати'),

                    Forms\Components\Select::make('role')
                        ->label('Роль')
                        ->options([
                            'client' => 'Клієнт',
                            'admin'  => 'Адмін',
                        ])
                        ->required()
                        ->default('client')
                        ->native(false),
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

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('orders_count')
                    ->label('Замовлень')
                    ->counts('orders')
                    ->sortable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('Роль')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin'  => 'danger',
                        'client' => 'warning',
                        default  => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'client' => 'Клієнт',
                        'admin'  => 'Адмін',
                        default  => $state,
                    }),


                // Загальна сума витрат клієнта
                Tables\Columns\TextColumn::make('orders_sum_total_price')
                    ->label('Витрачено всього')
                    ->sum('orders', 'total_price')
                    ->money('EUR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Зареєстрований')
                    ->date('d.m.Y')
                    ->sortable(),

            ])

            ->defaultSort('created_at', 'desc')

            ->filters([
                Tables\Filters\SelectFilter::make('role')  // ось сюди
                ->label('Роль')
                    ->options([
                        'client' => 'Клієнти',
                        'admin'  => 'Адміни',
                    ]),

                Tables\Filters\Filter::make('has_orders')
                    ->label('Є замовлення')
                    ->query(fn ($query) => $query->has('orders')),

                Tables\Filters\Filter::make('no_orders')
                    ->label('Без замовлень')
                    ->query(fn ($query) => $query->doesntHave('orders')),
            ])

            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            Infolists\Components\Section::make('Дані клієнта')
                ->columns(3)
                ->schema([
                    Infolists\Components\TextEntry::make('name')->label("Ім'я"),
                    Infolists\Components\TextEntry::make('email')->label('Email'),
                    Infolists\Components\TextEntry::make('created_at')
                        ->label('Зареєстрований')
                        ->date('d.m.Y'),
                ]),
        ]);
    }

    // -------------------------------------------------------------------------
    // RELATION MANAGERS
    // -------------------------------------------------------------------------

    public static function getRelationManagers(): array
    {
        return [
            RelationManagers\OrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view'   => Pages\ViewUser::route('/{record}'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
