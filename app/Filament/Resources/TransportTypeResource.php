<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\TransportType;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TransportTypeResource\Pages;
use App\Filament\Resources\TransportTypeResource\RelationManagers;

class TransportTypeResource extends Resource
{
    protected static ?string $model = TransportType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               Forms\Components\Select::make('mode')
                ->label('Mode')
                ->options([
                    'road' => 'Road',
                    'air'  => 'Air',
                    'rail' => 'Rail',
                ])
                ->required()
                ->rules(['in:road,air,rail'])
                ->native(false),

            TextInput::make('name')
    ->required()
    ->maxLength(100)
    ->live(onBlur: true)
    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
        // Only update code if the user has not customized it
        if (($get('code') ?? '') !== Str::slug($old, '_')) {
            return;
        }

        $set('code', Str::upper(Str::slug($state, '_')));
    }),

// Code field
TextInput::make('code')
    ->required()
    ->maxLength(50)
    ->unique(ignoreRecord: true)
    ->helperText('Auto-generated from name, but you can edit manually.'),

            Forms\Components\TextInput::make('capacity')
                ->label('Capacity')
                ->numeric()
                ->minValue(1)
                ->maxValue(200)
                ->helperText('Optional: seats or pax capacity'),

            Forms\Components\Textarea::make('description')
                ->columnSpanFull()
                ->rows(3),

            Forms\Components\Toggle::make('is_active')
                ->label('Active')
                ->default(true)
                ->required(),
        
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('mode'),
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('capacity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
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
            'index' => Pages\ListTransportTypes::route('/'),
            'create' => Pages\CreateTransportType::route('/create'),
            'edit' => Pages\EditTransportType::route('/{record}/edit'),
        ];
    }
}
