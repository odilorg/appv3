<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarResource\Pages;
use App\Filament\Resources\CarResource\RelationManagers;
use App\Models\Car;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CarResource extends Resource
{
    protected static ?string $model = Car::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('driver_id')
                    ->relationship('driver', 'name')
                    ->required(),
                Forms\Components\TextInput::make('make')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('model')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('year')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('plate_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('color')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('vin')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('seats')
                    ->required()
                    ->numeric()
                    ->default(4),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->required(),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('driver.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('make')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->searchable(),
                Tables\Columns\TextColumn::make('year')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('plate_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('color')
                    ->searchable(),
                Tables\Columns\TextColumn::make('vin')
                    ->searchable(),
                Tables\Columns\TextColumn::make('seats')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListCars::route('/'),
            'create' => Pages\CreateCar::route('/create'),
            'edit' => Pages\EditCar::route('/{record}/edit'),
        ];
    }
}
