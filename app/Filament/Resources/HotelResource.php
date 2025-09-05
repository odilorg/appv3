<?php

namespace App\Filament\Resources;

use App\Enums\HotelType;
use App\Models\Hotel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class HotelResource extends \Filament\Resources\Resource
{
    protected static ?string $model = Hotel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Suppliers';
    protected static ?string $navigationLabel = 'Hotels';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Hotel')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('city_id')
                        ->relationship('city', 'name')
                        ->required()
                        ->searchable()
                        ->preload(),

                    Forms\Components\Select::make('company_id')
                        ->relationship('company', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('address')
                        ->maxLength(255),

                    // CATEGORY: 1..5 stars with nice labels
                    Forms\Components\Select::make('category')
                        ->label('Category (Stars)')
                        ->options([
                            1 => '★☆☆☆☆',
                            2 => '★★☆☆☆',
                            3 => '★★★☆☆',
                            4 => '★★★★☆',
                            5 => '★★★★★',
                        ])
                        ->nullable()
                        ->native(false),

                    // TYPE: from enum
                    Forms\Components\Select::make('type')
                        ->label('Type')
                        ->options(HotelType::options())
                        ->nullable()
                        ->native(false),

                    Forms\Components\TextInput::make('phone')->tel()->maxLength(255),
                    Forms\Components\TextInput::make('email')->email()->maxLength(255),

                    Forms\Components\FileUpload::make('images')
                        ->label('Images')
                        ->multiple()
                        ->image()
                        ->directory('hotels')
                        ->downloadable()
                        ->reorderable()
                        ->appendFiles()
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('description')->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Amenities')
                ->schema([
                    Forms\Components\Select::make('amenities')
                        ->label('Hotel amenities')
                        ->multiple()
                        ->relationship(
                            name: 'amenities',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn ($query) => $query->where('scope', 'hotel')
                        )
                        ->preload()
                        ->searchable()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')->required(),
                            Forms\Components\Hidden::make('scope')->default('hotel'),
                        ])
                        ->createOptionUsing(fn ($data) =>
                            \App\Models\Amenity::firstOrCreate(
                                ['name' => $data['name']],
                                ['scope' => 'hotel']
                            )->id
                        ),
                ])->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('city.name')->label('City')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('company.name')->label('Company')->toggleable(),
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('address')->toggleable()->limit(40),

            Tables\Columns\TextColumn::make('category')
                ->label('Stars')
                ->formatStateUsing(fn ($state) => $state ? str_repeat('★', (int) $state) . str_repeat('☆', 5 - (int) $state) : '—')
                ->sortable(),

            Tables\Columns\TextColumn::make('type')
    ->badge()
    ->formatStateUsing(function ($state) {
        if ($state instanceof HotelType) {
            return $state->label();
        }
        if (is_string($state)) {
            return HotelType::tryFrom($state)?->label() ?? $state;
        }
        return '—';
    })
    ->sortable(),

            Tables\Columns\TextColumn::make('phone')->toggleable()->searchable(),
            Tables\Columns\TextColumn::make('email')->toggleable()->searchable(),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('type')->options(HotelType::options()),
            Tables\Filters\SelectFilter::make('category')->options([1=>1,2=>2,3=>3,4=>4,5=>5]),
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
            \App\Filament\Resources\HotelResource\RelationManagers\RoomsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Filament\Resources\HotelResource\Pages\ListHotels::route('/'),
            'create' => \App\Filament\Resources\HotelResource\Pages\CreateHotel::route('/create'),
            'edit'   => \App\Filament\Resources\HotelResource\Pages\EditHotel::route('/{record}/edit'),
        ];
    }
}
