<?php

namespace App\Filament\Resources\DriverResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CarsRelationManager extends RelationManager
{
    protected static string $relationship = 'Cars';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('make')
            ->columns([
                Tables\Columns\TextColumn::make('make'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
