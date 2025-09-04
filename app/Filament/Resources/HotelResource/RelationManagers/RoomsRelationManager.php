<?php

namespace App\Filament\Resources\HotelResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Amenity;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class RoomsRelationManager extends RelationManager
{
    protected static string $relationship = 'Rooms';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label('Room type')->required(),
            Forms\Components\Select::make('layout')
                ->label('Layout')
                ->native(false)
                ->options([
                    'single'=>'Single','twin'=>'Twin','double'=>'Double',
                    'triple'=>'Triple','suite'=>'Suite',
                ]),
            Forms\Components\TextInput::make('base_price_usd')
                ->label('Price per night ($)')
                ->numeric()
                ->minValue(0)
                ->suffix('$')
                ->required(),
            Forms\Components\TextInput::make('size_m2')->label('Size (m²)')->numeric(),
            Forms\Components\TextInput::make('max_adults')->numeric()->minValue(1)->default(2),
            Forms\Components\TextInput::make('max_children')->numeric()->minValue(0)->default(0),

            // Room amenities (create new with +)
            Forms\Components\Select::make('amenities')
                ->label('Room amenities')
                ->multiple()
                ->preload()
                ->searchable()
                ->relationship(name: 'amenities', titleAttribute: 'name',
                    modifyQueryUsing: fn ($query) => $query->where('scope','room')
                )
                ->createOptionForm([
                    Forms\Components\TextInput::make('name')->required()->maxLength(120),
                    Forms\Components\Hidden::make('scope')->default('room'),
                    Forms\Components\TextInput::make('icon')->label('Icon (optional)'),
                ])
                ->createOptionUsing(function (array $data) {
                    return Amenity::firstOrCreate(
                        ['name' => $data['name']],
                        ['scope' => 'room', 'icon' => $data['icon'] ?? null],
                    )->id;
                }),

            Forms\Components\RichEditor::make('description')->columnSpanFull(),

            Forms\Components\FileUpload::make('images')
                ->image()
                ->multiple()
                ->reorderable()
                ->directory('hotels/rooms')
                ->imageEditor()
                ->downloadable(),

            Forms\Components\Toggle::make('is_active')->default(true),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
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
