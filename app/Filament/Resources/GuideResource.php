<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Guide;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\GuideResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\GuideResource\RelationManagers;

class GuideResource extends Resource
{
    protected static ?string $model = Guide::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                            ->label('ФИО Гида')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('Телефон')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('address')
                            ->label('Адрес')
                            //->required()
                            ->maxLength(255),
                        TextInput::make('city')
                            ->label('Город')
                            //->required()
                            ->maxLength(255),
                Section::make('Languages & Levels')
                    ->schema([
                       Repeater::make('price_types')
                            ->label('Типы цен')
                            ->schema([
                                Select::make('price_type_name')
                                ->options([
                                    'pickup_dropoff' => 'Встреча/проводы',
                                    'halfday' => 'Полдня',
                                    'per_daily' => 'За день',
                                ])
                                ->required(),
                                TextInput::make('price')
                                ->required()
                                ->numeric()
                                ->prefix('$'),
                                // ...
                            ]),
                        Repeater::make('guideLanguages')
                            ->relationship() // binds to Guide::guideLanguages()
                            ->defaultItems(0)
                            ->schema([
                                // choose Language from master list
                                Select::make('language_id')
                                    ->label('Language')
                                    ->relationship('language', 'name') // uses GuideLanguage::language()
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    // prevent duplicate language per guide (nice UX error)
                                    ->rule(function (callable $get) {
                                        return Rule::unique('guide_language', 'language_id')
                                            ->where(fn($q) => $q->where('guide_id', request()->route('record')));
                                    })
                                    ->distinct(), // prevents same option twice in the repeater UI

                                // pick level from a fixed list (no typos)
                                Select::make('level')
                                    ->label('Level')
                                    ->options([
                                        'A1' => 'A1',
                                        'A2' => 'A2',
                                        'B1' => 'B1',
                                        'B2' => 'B2',
                                        'C1' => 'C1',
                                        'C2' => 'C2',
                                    ])
                                    ->required()
                                    ->native(false),
                            ])
                            ->addActionLabel('Add language')
                            ->grid(2) // language + level side by side
                            ->reorderable(false) // ordering not needed
                            ->collapsible(),
                    ]),
                    FileUpload::make('image')
                            ->label('Фото')
                            ->image(),
                        
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('ФИО Гида')
                    ->searchable(),

                // Show related Language names (comma-separated by default)
                TextColumn::make('languages.name')
                    ->label('Languages')
                    ->listWithLineBreaks()     // nicer than one long line
                    ->limitList(6)             // optional
                    ->wrap(),

                // Show pivot levels via the hasMany pivot model (GuideLanguage)
                TextColumn::make('guideLanguages.level')
                    ->label('Levels')
                    ->listWithLineBreaks()
                    ->wrap(),

               
               
                TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable(),
                TextColumn::make('email')
                    ->icon('heroicon-m-envelope')
                    ->iconColor('primary')
                    ->copyable()
                    ->copyMessage('Email address copied')
                    ->copyMessageDuration(1500)
                    ->searchable(),
                    TextColumn::make('price_types')
                        ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state)

    ->listWithLineBreaks(),
                TextColumn::make('address')
                    ->label('Адрес')
                    ->searchable()
                    ->limit(25)
                    ->wrap(),
                TextColumn::make('city')
                    ->label('Город')
                    ->searchable(),
                ImageColumn::make('image')
                    ->label('Фото'),
                //->thumbnail()
                //->sortable()
                //->searchable(),    
                
              



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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGuides::route('/'),
            'create' => Pages\CreateGuide::route('/create'),
            'edit' => Pages\EditGuide::route('/{record}/edit'),
        ];
    }
}
