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

class VehicleTypeResource extends Resource
{
    protected static ?string $model = VehicleType::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Типи Авто';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Основна інформація')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Назва авто')
                                ->required()
                                ->placeholder('Наприклад: Sprinter Maxi'),
                            
                            TextInput::make('slug')
                                ->label('URL-ім\'я (slug)')
                                ->required()
                                ->unique(ignoreRecord: true),
                        ]),

                        FileUpload::make('image_path')
                            ->label('Фото автомобіля')
                            ->image()
                            ->directory('vehicles')
                            ->visibility('public')
                            ->columnSpanFull(),
                            
                        Textarea::make('description')
                            ->label('Опис для клієнта')
                            ->rows(3)
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

                Toggle::make('is_active')
                    ->label('Активний (показувати на сайті)')
                    ->default(true),
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
                //
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