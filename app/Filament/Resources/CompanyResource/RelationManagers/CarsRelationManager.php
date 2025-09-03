<?php

namespace App\Filament\Resources\CompanyResource\RelationManagers;

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
                  Forms\Components\Select::make('driver_id')
                ->label('Driver')
                ->relationship('driver','name')
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\TextInput::make('plate_number')->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('make')->required(),
            Forms\Components\TextInput::make('model')->required(),
            Forms\Components\TextInput::make('year')->numeric(),
            Forms\Components\TextInput::make('color'),
            Forms\Components\TextInput::make('vin')->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('seats')->numeric()->minValue(1)->maxValue(60)->default(4),
            Forms\Components\Toggle::make('is_active')->default(true),
            Forms\Components\Textarea::make('notes')->columnSpanFull(),
        ])->columns(2);
            
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('brand')
            ->columns([
                Tables\Columns\TextColumn::make('brand'),
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
