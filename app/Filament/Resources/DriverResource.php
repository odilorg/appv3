<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Driver;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\DriverResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DriverResource\RelationManagers;
use App\Filament\Resources\DriverResource\RelationManagers\CarsRelationManager;

class DriverResource extends Resource
{
    protected static ?string $model = Driver::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                   Forms\Components\Section::make('Driver')
                ->schema([
                    Forms\Components\TextInput::make('name')->required()->maxLength(120),
                    Forms\Components\TextInput::make('phone01')->tel()->maxLength(50),
                    Forms\Components\TextInput::make('phone02')->tel()->maxLength(50),
                    Forms\Components\TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->maxLength(255),
                              Forms\Components\TextInput::make('address')
                            ->label('Address')
                            ->required()
                            ->maxLength(255),
                    Forms\Components\TextInput::make('license_number')->maxLength(80)->unique(ignoreRecord: true),
                    Forms\Components\DatePicker::make('license_expires_at'),
                    Forms\Components\FileUpload::make('image')
                            ->image(),
                            Forms\Components\FileUpload::make('license_image')
                            ->image(),
                    Forms\Components\Toggle::make('is_active')->default(true),
                    Forms\Components\Textarea::make('notes')->columnSpanFull(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('phone01')->toggleable(),
                Tables\Columns\TextColumn::make('phone02')->toggleable(),
                Tables\Columns\TextColumn::make('license_number')->toggleable(),
                Tables\Columns\TextColumn::make('license_expires_at')->date()->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('cars_count')
                    ->counts('cars')
                    ->label('Cars')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
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
            CarsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDrivers::route('/'),
            'create' => Pages\CreateDriver::route('/create'),
            'edit' => Pages\EditDriver::route('/{record}/edit'),
        ];
    }
}
