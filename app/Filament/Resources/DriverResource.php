<?php

namespace App\Filament\Resources;

use App\Enums\CustomerType;
use App\Filament\Resources\DriverResource\Pages;
use App\Filament\Resources\DriverResource\RelationManagers\CarsRelationManager;
use App\Models\Driver;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class DriverResource extends \Filament\Resources\Resource
{
    protected static ?string $model = Driver::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Suppliers';
    protected static ?string $navigationLabel = 'Drivers';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Driver')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('type')
                        ->label('Type')
                        ->options(CustomerType::options()) // individual | company
                        ->required()
                        ->default(CustomerType::INDIVIDUAL->value)
                        ->live(),

                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(120),

                    // INDIVIDUAL contact fields
                    Forms\Components\TextInput::make('phone01')
                        ->tel()->maxLength(50)
                        ->required(),
                    Forms\Components\TextInput::make('phone02')->tel()->maxLength(50)->required(),
                    Forms\Components\TextInput::make('email')
                        ->label('Email address')
                        ->email()->maxLength(255)
                        ->required()
                        ->label('Address')
                        ->maxLength(255)
                        ->visible(fn (Forms\Get $get) => $get('type') === CustomerType::INDIVIDUAL->value),

                    // COMPANY picker (required when type=company)
                    Forms\Components\Select::make('company_id')
                        ->label('Company')
                        ->relationship('company', 'name')
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')->required()->maxLength(255),
                            Forms\Components\TextInput::make('address_street')->maxLength(255),
                            Forms\Components\TextInput::make('address_city')->maxLength(255),
                            Forms\Components\TextInput::make('phone')->maxLength(255),
                            Forms\Components\TextInput::make('email')->email()->maxLength(255),
                            Forms\Components\TextInput::make('inn')->maxLength(255),
                            Forms\Components\TextInput::make('account_number')->maxLength(255),
                            Forms\Components\TextInput::make('bank_name')->maxLength(255),
                            Forms\Components\TextInput::make('bank_mfo')->maxLength(255),
                            Forms\Components\TextInput::make('director_name')->maxLength(255),
                            Forms\Components\TextInput::make('license_number')->maxLength(255),
                            Forms\Components\Toggle::make('is_operator')->default(false),
                        ])
                        ->visible(fn (Forms\Get $get) => $get('type') === CustomerType::COMPANY->value)
                        ->required(fn (Forms\Get $get) => $get('type') === CustomerType::COMPANY->value),

                    Forms\Components\FileUpload::make('image')->image(),
                    Forms\Components\Toggle::make('is_active')->default(true),

                    Forms\Components\TextInput::make('license_number')
                        ->maxLength(80)
                        ->unique(ignoreRecord: true),

                    Forms\Components\DatePicker::make('license_expires_at'),
                    Forms\Components\FileUpload::make('license_image')->image(),

                    Forms\Components\Textarea::make('notes')->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),

            Tables\Columns\TextColumn::make('type')->badge()->sortable(),

            Tables\Columns\TextColumn::make('company.name')
                ->label('Company')
                ->toggleable(),

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
            Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('deleted_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('type')->options(CustomerType::options()),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ])
        ->emptyStateActions([
            Tables\Actions\CreateAction::make(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            CarsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDrivers::route('/'),
            'create' => Pages\CreateDriver::route('/create'),
            'edit'   => Pages\EditDriver::route('/{record}/edit'),
        ];
    }
}
