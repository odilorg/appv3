<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RateItemResource\Pages;
use App\Models\RateItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class RateItemResource extends Resource
{
    protected static ?string $model = RateItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Pricing';
    protected static ?string $navigationLabel = 'Rate Items';
    protected static ?string $modelLabel = 'Rate Item';
    protected static ?string $pluralModelLabel = 'Rate Items';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Group::make()
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('mode')
                        ->label('Mode')
                        ->options([
                            'road' => 'Road',
                            'air'  => 'Air',
                            'rail' => 'Rail',
                        ])
                        ->required()
                        ->native(false)
                        ->rules(['in:road,air,rail']),

                    Forms\Components\Select::make('default_unit')
                        ->label('Default unit')
                        ->options([
                            'flat'       => 'Flat (per service)',
                            'per_hour'   => 'Per hour',
                            'per_km'     => 'Per km',
                            'per_ticket' => 'Per ticket',
                        ])
                        ->required()
                        ->native(false)
                        ->rules(['in:flat,per_hour,per_km,per_ticket']),
                ]),

            Forms\Components\Group::make()
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Name')
                        ->required()
                        ->maxLength(150)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                            // Only overwrite code if user hasn't customized it:
                            // compare current code to slug of OLD name.
                            $current = (string) ($get('code') ?? '');
                            $oldAuto = Str::upper(Str::slug((string) $old, '_'));
                            if ($current !== '' && $current !== $oldAuto) {
                                return;
                            }
                            $set('code', Str::upper(Str::slug((string) $state, '_')));
                        }),

                    Forms\Components\TextInput::make('code')
                        ->label('Code')
                        ->required()
                        ->maxLength(80)
                        ->unique(ignoreRecord: true)
                        ->rule('alpha_dash') // letters, numbers, dashes & underscores
                        ->helperText('Auto-generated from name; you can edit. Use letters/numbers/underscore.'),
                ]),

            Forms\Components\Textarea::make('description')
                ->label('Description')
                ->rows(3)
                ->columnSpanFull(),

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
                Tables\Columns\TextColumn::make('mode')
                    ->label('Mode')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'road' => 'Road',
                        'air'  => 'Air',
                        'rail' => 'Rail',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('default_unit')
                    ->label('Unit')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'flat'       => 'Flat',
                        'per_hour'   => 'Per hour',
                        'per_km'     => 'Per km',
                        'per_ticket' => 'Per ticket',
                        default      => $state,
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('mode')
                    ->label('Mode')
                    ->options([
                        'road' => 'Road',
                        'air'  => 'Air',
                        'rail' => 'Rail',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
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
            // You can add a TransportRates relation manager later if you want inline rates.
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRateItems::route('/'),
            'create' => Pages\CreateRateItem::route('/create'),
            'edit'   => Pages\EditRateItem::route('/{record}/edit'),
        ];
    }
}
