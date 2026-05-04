<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleTypeResource\Pages;
use App\Models\VehicleType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Tabs;

class VehicleTypeResource extends Resource
{
    protected static ?string $model = VehicleType::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Типи Авто';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Локалізація назви та опису')
                    ->description('Введіть дані для кожної мови окремо')
                    ->schema([
                        Tabs::make('Languages')
                            ->tabs([
                                // Німецька (Основна)
                                Tabs\Tab::make('Deutsch')
                                    ->icon('heroicon-m-language')
                                    ->schema([
                                        TextInput::make('name_de')
                                            ->label('Name (DE)')
                                            ->required()
                                            ->placeholder('z.B. Sprinter Maxi'),
                                        Textarea::make('description_de')
                                            ->label('Beschreibung (DE)')
                                            ->rows(3),
                                    ]),

                                // Українська
                                Tabs\Tab::make('Українська')
                                    ->icon('heroicon-m-language')
                                    ->schema([
                                        TextInput::make('name_uk')
                                            ->label('Назва (UK)')
                                            ->required()
                                            ->placeholder('Наприклад: Спрінтер Максі'),
                                        Textarea::make('description_uk')
                                            ->label('Опис (UK)')
                                            ->rows(3),
                                    ]),

                                // Англійська
                                Tabs\Tab::make('English')
                                    ->icon('heroicon-m-language')
                                    ->schema([
                                        TextInput::make('name_en')
                                            ->label('Name (EN)')
                                            ->placeholder('e.g. Sprinter Maxi'),
                                        Textarea::make('description_en')
                                            ->label('Description (EN)')
                                            ->rows(3),
                                    ]),

                                // Російська
                                Tabs\Tab::make('Русский')
                                    ->icon('heroicon-m-language')
                                    ->schema([
                                        TextInput::make('name_ru')
                                            ->label('Название (RU)')
                                            ->placeholder('Например: Спринтер Макси'),
                                        Textarea::make('description_ru')
                                            ->label('Описание (RU)')
                                            ->rows(3),
                                    ]),
                            ])->columnSpanFull(),
                    ]),

                Section::make('Технічні параметри та Медіа')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('slug')
                                ->label('URL-ім\'я (slug)')
                                ->required()
                                ->unique(ignoreRecord: true),

                            Toggle::make('is_active')
                                ->label('Активний (показувати на сайті)')
                                ->default(true)
                                ->inline(false),
                        ]),

                        FileUpload::make('image_path')
                            ->label('Фото автомобіля')
                            ->image()
                            ->disk('public')
                            ->directory('vehicles')
                            ->visibility('public')
                            ->columnSpanFull(),
                    ]),

                Section::make('Габарити та Вантаж')
                    ->schema([
                        Grid::make(4)->schema([
                            TextInput::make('length_m')->label('Довжина (м)')->numeric()->step(0.1),
                            TextInput::make('width_m')->label('Ширина (м)')->numeric()->step(0.1),
                            TextInput::make('height_m')->label('Висота (м)')->numeric()->step(0.1),
                            TextInput::make('volume_m3')->label('Об\'єм (м³)')->numeric()->step(0.1),
                        ]),
                        TextInput::make('max_weight_kg')
                            ->label('Вантажопідйомність (кг)')
                            ->numeric()
                            ->suffix('кг'),
                    ]),

                Section::make('Тарифи')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('start_price')
                                ->label('Ціна подачі (€)')
                                ->numeric()
                                ->prefix('€'),

                            TextInput::make('price_per_km')
                                ->label('Ціна за 1 км (€)')
                                ->numeric()
                                ->prefix('€'),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')->label('Фото'),
                Tables\Columns\TextColumn::make('name')->label('Назва')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('price_per_km')->label('€/км')->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')->label('Активний'),
            ])
            ->filters([
                // Активність — три стани: всі / тільки активні / тільки неактивні
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Активність')
                    ->trueLabel('Тільки активні')
                    ->falseLabel('Тільки неактивні')
                    ->placeholder('Всі'),

                // Чи заповнений тариф
                Tables\Filters\Filter::make('has_price')
                    ->label('Є тариф (€/км)')
                    ->query(fn (Builder $query) => $query->whereNotNull('price_per_km')->where('price_per_km', '>', 0)),

                // Чи заповнені габарити
                Tables\Filters\Filter::make('has_dimensions')
                    ->label('Є габарити')
                    ->query(fn (Builder $query) => $query->whereNotNull('length_m')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListVehicleTypes::route('/'),
            'create' => Pages\CreateVehicleType::route('/create'),
            'edit' => Pages\EditVehicleType::route('/{record}/edit'),
        ];
    }
}
