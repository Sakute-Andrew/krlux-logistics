<?php

// app/Filament/Resources/PromoCodeResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoCodeResource\Pages;
use App\Models\PromoCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PromoCodeResource extends Resource
{
    protected static ?string $model = PromoCode::class;

    protected static ?string $navigationIcon   = 'heroicon-o-tag';
    protected static ?string $navigationLabel  = 'Промокоди';
    protected static ?string $modelLabel       = 'Промокод';
    protected static ?string $pluralModelLabel = 'Промокоди';
    protected static ?int    $navigationSort   = 4;

    // -------------------------------------------------------------------------
    // FORM
    // -------------------------------------------------------------------------

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Налаштування коду')
                ->icon('heroicon-o-tag')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('code')
                        ->label('Промокод')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(50)
                        ->placeholder('MUNICH10')
                        ->helperText('Тільки латиниця та цифри, у верхньому регістрі'),

                    Forms\Components\Select::make('type')
                        ->label('Тип знижки')
                        ->options([
                            'percent' => 'Відсоток (%)',
                            'fixed'   => 'Фіксована сума (€)',
                        ])
                        ->required()
                        ->native(false)
                        ->live(), // реактивно — щоб suffix/prefix змінювались

                    Forms\Components\TextInput::make('value')
                        ->label('Розмір знижки')
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->suffix(fn (Forms\Get $get) => $get('type') === 'percent' ? '%' : '€'),

                    Forms\Components\TextInput::make('min_order_price')
                        ->label('Мінімальна сума замовлення')
                        ->numeric()
                        ->nullable()
                        ->prefix('€')
                        ->placeholder('Без обмеження'),
                ]),

            Forms\Components\Section::make('Обмеження')
                ->icon('heroicon-o-clock')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('usage_limit')
                        ->label('Ліміт використань')
                        ->numeric()
                        ->nullable()
                        ->minValue(1)
                        ->placeholder('Необмежено'),

                    Forms\Components\DateTimePicker::make('expires_at')
                        ->label('Діє до')
                        ->seconds(false)
                        ->nullable()
                        ->placeholder('Без терміну'),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Активний')
                        ->default(true)
                        ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('code')
                    ->label('Код')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Скопійовано!')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('type')
                    ->label('Тип')
                    ->badge()
                    ->color(fn (string $state) => $state === 'percent' ? 'info' : 'warning')
                    ->formatStateUsing(fn (string $state) => $state === 'percent' ? 'Відсоток' : 'Фіксована'),

                Tables\Columns\TextColumn::make('value')
                    ->label('Знижка')
                    ->formatStateUsing(fn ($state, PromoCode $record) =>
                    $record->type === 'percent' ? "{$state}%" : "€{$state}"
                    ),

                Tables\Columns\TextColumn::make('usage_count')
                    ->label('Використань')
                    ->formatStateUsing(fn ($state, PromoCode $record) =>
                    $record->usage_limit
                        ? "{$state} / {$record->usage_limit}"
                        : "{$state} / ∞"
                    ),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Діє до')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('Безстроково')
                    ->color(fn ($state) => $state && \Carbon\Carbon::parse($state)->isPast() ? 'danger' : null),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Активний'),
            ])

            ->defaultSort('created_at', 'desc')

            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Активність')
                    ->trueLabel('Тільки активні')
                    ->falseLabel('Тільки неактивні')
                    ->placeholder('Всі'),

                Tables\Filters\SelectFilter::make('type')
                    ->label('Тип знижки')
                    ->options([
                        'percent' => 'Відсоток',
                        'fixed'   => 'Фіксована',
                    ]),

                Tables\Filters\Filter::make('expired')
                    ->label('Прострочені')
                    ->query(fn (Builder $query) => $query->whereNotNull('expires_at')->where('expires_at', '<', now())),

                Tables\Filters\Filter::make('limit_reached')
                    ->label('Ліміт вичерпано')
                    ->query(fn (Builder $query) => $query->whereNotNull('usage_limit')->whereColumn('usage_count', '>=', 'usage_limit')),
            ])

            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Деактивувати')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false])),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPromoCodes::route('/'),
            'create' => Pages\CreatePromoCode::route('/create'),
            'edit'   => Pages\EditPromoCode::route('/{record}/edit'),
        ];
    }
}
