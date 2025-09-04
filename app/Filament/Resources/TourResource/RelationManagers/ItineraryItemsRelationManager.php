<?php

namespace App\Filament\Resources\TourResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ItineraryItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class ItineraryItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'itineraryItems';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('type')
                        ->label('Type')
                        ->native(false)
                        ->options([
                            'day'  => 'Day',
                            'stop' => 'Stop',
                        ])
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (Set $set, $state) {
                            // If we switch to 'day', remove parent_id
                            if ($state === 'day') {
                                $set('parent_id', null);
                            }
                        }),

                    Forms\Components\TextInput::make('title')
                        ->label('Title')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->rows(3)
                        ->columnSpanFull(),

                    // Only allow choosing a parent "Day" within the same Tour
                    Forms\Components\Select::make('parent_id')
                        ->label('Parent Day')
                        ->native(false)
                        ->placeholder('— No parent (top-level Day) —')
                        ->visible(fn (Get $get) => $get('type') === 'stop')
                        ->options(function (RelationManager $livewire) {
                            /** @var \App\Models\Tour $tour */
                            $tour = $livewire->getOwnerRecord();

                            return ItineraryItem::query()
                                ->where('tour_id', $tour->id)
                                ->whereNull('parent_id')        // top-level
                                ->where('type', 'day')
                                //->orderBy('sort_order')
                                ->pluck('title', 'id');
                        })
                        ->searchable(),

                    Forms\Components\TimePicker::make('default_start_time')
                        ->label('Default Start Time')
                        ->seconds(false),

                    Forms\Components\TextInput::make('duration_minutes')
                        ->label('Planned Duration (min)')
                        ->numeric()
                        ->minValue(0),

                    Forms\Components\KeyValue::make('meta')
                        ->label('Meta (optional)')
                        ->columnSpanFull()
                        ->keyLabel('Key')
                        ->valueLabel('Value')
                      //  ->addButtonLabel('Add meta'),
                ]),
        
            ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title'),
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
