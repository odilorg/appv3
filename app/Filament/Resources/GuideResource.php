<?php

namespace App\Filament\Resources;

use App\Enums\CustomerType;
use App\Filament\Resources\GuideResource\Pages;
use App\Models\Guide;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class GuideResource extends \Filament\Resources\Resource
{
    protected static ?string $model = Guide::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Suppliers';
    protected static ?string $navigationLabel = 'Guides';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Guide')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('type')
                        ->label('Type')
                        ->options(CustomerType::options()) // individual|company
                        ->required()
                        ->default(CustomerType::INDIVIDUAL->value)
                        ->live(),

                    Forms\Components\TextInput::make('name')
                        ->label('Name / Contact')
                        ->required()
                        ->maxLength(255),

                    // INDIVIDUAL contact fields (visible for individual)
                    Forms\Components\TextInput::make('phone')
                        ->label('Phone')
                        ->maxLength(255)
                        ->required(fn (Forms\Get $get) => $get('type') === CustomerType::INDIVIDUAL->value),

                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(255)
                        ->required(fn (Forms\Get $get) => $get('type') === CustomerType::INDIVIDUAL->value),

                    Forms\Components\TextInput::make('address')
                        ->label('Address')
                        ->maxLength(255)
                        ->visible(fn (Forms\Get $get) => $get('type') === CustomerType::INDIVIDUAL->value),

                    Forms\Components\TextInput::make('city')
                        ->label('City')
                        ->maxLength(255)
                        ->visible(fn (Forms\Get $get) => $get('type') === CustomerType::INDIVIDUAL->value),

                    // COMPANY picker (visible for company)
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

                    Forms\Components\FileUpload::make('image')
                        ->label('Photo')
                        ->image()
                        ->directory('guides'), // optional
                ]),

            Forms\Components\Section::make('Rates')
                ->schema([
                    Forms\Components\Repeater::make('price_types')
                        ->label('Price Types')
                        ->defaultItems(0)
                        ->schema([
                            Forms\Components\Select::make('price_type_name')
                                ->options([
                                    'pickup_dropoff' => 'Pickup/Dropoff',
                                    'halfday'        => 'Half Day',
                                    'per_daily'      => 'Per Day',
                                ])
                                ->required(),
                            Forms\Components\TextInput::make('price')
                                ->numeric()
                                ->minValue(0)
                                ->prefix('$')
                                ->required(),
                        ])
                        ->addActionLabel('Add price'),
                ]),

            Forms\Components\Section::make('Languages & Levels')
                ->schema([
                    Forms\Components\Repeater::make('guideLanguages')
                        ->relationship() // uses Guide::guideLanguages()
                        ->defaultItems(0)
                        ->schema([
                            Forms\Components\Select::make('language_id')
                                ->label('Language')
                                ->relationship('language', 'name') // GuideLanguage::language()
                                ->searchable()
                                ->preload()
                                ->required()
                                ->distinct(), // UI-level (DB unique index will enforce too)
                            Forms\Components\Select::make('level')
                                ->label('Level')
                                ->options([
                                    'A1' => 'A1', 'A2' => 'A2',
                                    'B1' => 'B1', 'B2' => 'B2',
                                    'C1' => 'C1', 'C2' => 'C2',
                                ])
                                ->required()
                                ->native(false),
                        ])
                        ->addActionLabel('Add language')
                        ->grid(2)
                        ->reorderable(false)
                        ->collapsible(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')
                ->label('Name / Contact')
                ->searchable(),

            Tables\Columns\TextColumn::make('type')
                ->badge()
                ->sortable(),

            Tables\Columns\TextColumn::make('company.name')
                ->label('Company')
                ->visible(fn () => true) // shown; data will be blank for individuals
                ->toggleable(),

            Tables\Columns\TextColumn::make('languages.name')
                ->label('Languages')
                ->listWithLineBreaks()
                ->limitList(6)
                ->wrap(),

            Tables\Columns\TextColumn::make('guideLanguages.level')
                ->label('Levels')
                ->listWithLineBreaks()
                ->wrap(),

            Tables\Columns\TextColumn::make('phone')->label('Phone')->toggleable()->searchable(),
            Tables\Columns\TextColumn::make('email')->label('Email')->toggleable()->searchable(),

            // Render price_types nicely (name: $price)
            Tables\Columns\TextColumn::make('price_types')
                ->label('Rates')
                ->formatStateUsing(function ($state) {
                    if (!is_array($state)) return $state;
                    return collect($state)->map(function ($row) {
                        if (!is_array($row)) return (string) $row;
                        $label = match ($row['price_type_name'] ?? '') {
                            'pickup_dropoff' => 'Pickup/Dropoff',
                            'halfday'        => 'Half Day',
                            'per_daily'      => 'Per Day',
                            default          => $row['price_type_name'] ?? 'Type',
                        };
                        $price = $row['price'] ?? null;
                        return $price !== null ? "{$label}: \${$price}" : $label;
                    })->implode("\n");
                })
                ->listWithLineBreaks()
                ->wrap()
                ->toggleable(),

            Tables\Columns\ImageColumn::make('image')->label('Photo')->circular(),
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

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListGuides::route('/'),
            'create' => Pages\CreateGuide::route('/create'),
            'edit'   => Pages\EditGuide::route('/{record}/edit'),
        ];
    }
}
