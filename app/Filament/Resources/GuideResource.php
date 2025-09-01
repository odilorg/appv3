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
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
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
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Section::make('Languages & Levels')
                    ->schema([
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Guide')
                    ->searchable()
                    ->sortable(),

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
