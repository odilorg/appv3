<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Restaurant;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ToggleButtons;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RestaurantResource\Pages;
use App\Filament\Resources\RestaurantResource\RelationManagers;

class RestaurantResource extends Resource
{
    protected static ?string $model = Restaurant::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic')
                    ->schema([
                        Forms\Components\TextInput::make('name')->required()->maxLength(180),
                        Forms\Components\Select::make('city_id')->relationship('city', 'name')->required()->searchable()->preload(),
                        Forms\Components\Select::make('company_id')->relationship('company', 'name')->searchable()->preload()->nullable(),
                        Forms\Components\TextInput::make('address'),
                    ])->columns(2),

                Forms\Components\Section::make('Details')
                    ->schema([
                        Forms\Components\Select::make('cuisine')->options([
                            'uzbek' => 'Uzbek',
                            'chinese' => 'Chinese',
                            'italian' => 'Italian',
                            'european' => 'European',
                            'korean' => 'Korean',
                            'indian' => 'Indian',
                            'seafood' => 'Seafood',
                            'grill' => 'Grill',
                            'other' => 'Other',
                        ])->native(false)->searchable()->nullable(),
                        Forms\Components\Select::make('price_level')->options(['$' => '$', '$$' => '$$', '$$$' => '$$$', '$$$$' => '$$$$'])->native(false),
                        Forms\Components\Toggle::make('is_active')->default(true),
                        Forms\Components\TextInput::make('phone')->tel(),
                        Forms\Components\TextInput::make('email')->email(),
                        Forms\Components\TextInput::make('website')->url(),
                    ])->columns(4),



                Forms\Components\Section::make('Meals & Drinks')
    ->schema([
        // FULL-WIDTH meals list
        Forms\Components\Repeater::make('meals')
            ->relationship()
            ->label('Meals')
            ->addActionLabel('Add meal')
           // ->orderable('sort_order')
            ->collapsed()
            ->cloneable()
           ->itemLabel(fn (array $state): ?string =>
    trim(
        ($state['name'] ?? 'New item') .
        (filled($state['price'] ?? null)
            ? ' — ' . number_format((float) $state['price'], 2) . ' ' . ($state['currency'] ?? '')
            : ''
        )
    )
)
            ->schema([
                Forms\Components\Grid::make(12)->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Item name')->required()->maxLength(120)
                        ->columnSpan(4),

                    ToggleButtons::make('type')
                        ->options([
                            'breakfast'=>'Breakfast','lunch'=>'Lunch','dinner'=>'Dinner',
                            'snack'=>'Snack','drink'=>'Drink','other'=>'Other',
                        ])->inline()->nullable()
                        ->columnSpan(4),

                    Forms\Components\TextInput::make('price')
                        ->numeric()->minValue(0)->step('0.01')->required()
                        ->prefixIcon('heroicon-m-banknotes')
                        ->columnSpan(2),

                    Forms\Components\Select::make('currency')
                        ->options(['UZS'=>'UZS','USD'=>'USD','EUR'=>'EUR'])
                        ->default('UZS')->native(false)->searchable()
                        ->columnSpan(2),

                    ToggleButtons::make('per_person')
                        ->label('Charge basis')->inline()
                        ->options([1=>'Per person', 0=>'Per group'])
                        ->default(1)
                        ->columnSpan(3),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')->default(true)
                        ->columnSpan(1),

                    Forms\Components\Textarea::make('description')
                        ->rows(2)
                        ->placeholder('Optional notes…')
                        ->columnSpan(12),
                ]),
            ])
            ->columnSpanFull(),

        // Images moved to the bottom, still full width
        Forms\Components\FileUpload::make('images')
            ->label('Images')
            ->image()->multiple()->reorderable()
            ->directory('restaurants/images')
            ->imageEditor()->downloadable()
            ->panelLayout('grid')
            ->columnSpanFull(),
    ])
    ->columns(12),
                        // Forms\Components\Section::make('Media & Hours')
                        //     ->schema([
                                
                        //         // Forms\Components\KeyValue::make('opening_hours')
                        //         //     ->keyLabel('Day')->valueLabel('Hours')
                        //         //     ->helperText('Example: Mon = 10:00–22:00'),
                        //     ])->columns(1),

                    
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('city.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cuisine')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price_level')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('website')
                    ->searchable(),
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
            'index' => Pages\ListRestaurants::route('/'),
            'create' => Pages\CreateRestaurant::route('/create'),
            'edit' => Pages\EditRestaurant::route('/{record}/edit'),
        ];
    }
}
