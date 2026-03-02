<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
// --- ІМПОРТИ КОМПОНЕНТІВ ---
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
// ----------------------------

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart'; // Іконка кошика
    protected static ?string $navigationLabel = 'Замовлення';
    protected static ?int $navigationSort = 2; // Показувати після Типів Авто

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
                // --- Секція 1: Дані Клієнта ---
                Section::make('Інформація про клієнта')
                    ->description('Хто робить замовлення')
                    ->schema([
                        Grid::make(2)->schema([
                            // Якщо замовлення від зареєстрованого юзера
                            Select::make('user_id')
                                ->label('Зареєстрований клієнт')
                                ->relationship('user', 'name') // Показує ім'я, зберігає ID
                                ->searchable()
                                ->preload()
                                ->placeholder('Виберіть зі списку (якщо є)'),

                            TextInput::make('customer_name')
                                ->label('Ім\'я замовника (для гостей)')
                                ->required(), // Можна зробити required, якщо завжди пишемо ім'я
                                
                            TextInput::make('phone')
                                ->label('Телефон')
                                ->tel()
                                ->required(),
                                
                            TextInput::make('email')
                                ->label('Email')
                                ->email(),
                        ]),
                    ]),

                // --- Секція 2: Деталі перевезення ---
                Section::make('Деталі перевезення')
                    ->schema([
                        Grid::make(2)->schema([
                            // Вибір типу авто (зв'язок з vehicle_types)
                            Select::make('vehicle_type_id')
                                ->label('Тип автомобіля')
                                ->relationship('vehicleType', 'name')
                                ->required()
                                ->searchable()
                                ->preload(),

                            DateTimePicker::make('scheduled_at')
                                ->label('Дата та час подачі')
                                ->required()
                                ->native(false), // Використовує красивий календар Filament
                        ]),

                        Grid::make(2)->schema([
                            Textarea::make('pickup_address')
                                ->label('Звідки забрати')
                                ->required()
                                ->rows(3),
                                
                            Textarea::make('delivery_address')
                                ->label('Куди доставити')
                                ->required()
                                ->rows(3),
                        ]),
                        
                        Grid::make(3)->schema([
                            TextInput::make('distance_km')
                                ->label('Відстань (км)')
                                ->numeric()
                                ->suffix('км'),
                                
                            TextInput::make('total_price')
                                ->label('Загальна вартість')
                                ->numeric()
                                ->prefix('€')
                                ->required(),
                                
                            Select::make('status')
                                ->label('Статус')
                                ->options([
                                    'new' => 'Нове',
                                    'processing' => 'В роботі',
                                    'completed' => 'Виконано',
                                    'cancelled' => 'Скасовано',
                                ])
                                ->default('new')
                                ->required(),
                        ]),
                    ]),

                // --- Секція 3: Нотатки ---
                Section::make('Нотатки')
                    ->collapsed() // Згорнута за замовчуванням
                    ->schema([
                        Textarea::make('customer_note')
                            ->label('Коментар клієнта')
                            ->rows(2),
                            
                        Textarea::make('admin_note')
                            ->label('Внутрішня нотатка адміна')
                            ->rows(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('customer_name')->label('Клієнт')->searchable(),
                Tables\Columns\TextColumn::make('vehicleType.name')->label('Авто'), // Показати назву авто
                Tables\Columns\TextColumn::make('total_price')->label('Ціна')->money('eur'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'info',
                        'processing' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Створено'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}