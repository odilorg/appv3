<?php
namespace App\Filament\Resources;

use App\Filament\Resources\TransportRateResource\Pages;
use App\Models\TransportRate;
use App\Models\RateItem;
use App\Models\TransportType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;

class TransportRateResource extends Resource
{
    protected static ?string $model = TransportRate::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Pricing';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Group::make()
                ->columns(2)
                ->schema([
                    // 1) Pick the Rate Item (defines the service & default unit & mode)
                    Forms\Components\Select::make('rate_item_id')
                        ->label('Rate Item')
                        ->relationship('rateItem', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live() // so dependent fields can react
                        ->afterStateUpdated(function (Get $get, Set $set, $state) {
                            // When Rate Item changes: prefill unit and clear transport type if mode mismatches
                            $item = RateItem::query()->find($state);
                            if ($item) {
                                // Prefill unit only if empty
                                if (blank($get('unit'))) {
                                    $set('unit', $item->default_unit);
                                }
                                // If a transport type is selected but mode mismatches, clear it
                                $currentTypeId = $get('transport_type_id');
                                if ($currentTypeId) {
                                    $tt = TransportType::find($currentTypeId);
                                    if ($tt && $tt->mode !== $item->mode) {
                                        $set('transport_type_id', null);
                                    }
                                }
                            }
                        }),

                    // 2) Filter Transport Types by the selected Rate Item's mode
                    Forms\Components\Select::make('transport_type_id')
                        ->label('Transport Type')
                        ->searchable()
                        ->required()
                        ->options(function (Get $get) {
                            $rateItemId = $get('rate_item_id');
                            if (!$rateItemId) {
                                return TransportType::query()
                                    ->where('is_active', true)
                                    ->orderBy('name')
                                    ->pluck('name', 'id');
                            }
                            $item = RateItem::query()->find($rateItemId);
                            if (!$item) return [];
                            return TransportType::query()
                                ->where('mode', $item->mode) // align modes
                                ->where('is_active', true)
                                ->orderBy('name')
                                ->pluck('name', 'id');
                        })
                        ->helperText('Only types matching the Rate Item’s mode are shown.'),

                    // 3) Unit (optional override, defaults from RateItem)
                    Forms\Components\Select::make('unit')
                        ->label('Unit')
                        ->options([
                            'flat'       => 'Flat (per service)',
                            'per_hour'   => 'Per hour',
                            'per_km'     => 'Per km',
                            'per_ticket' => 'Per ticket',
                        ])
                        ->native(false)
                        ->helperText('Leave empty to use the Rate Item’s default unit.'),

                    // 4) Amount & Currency
                    Forms\Components\TextInput::make('amount')
                        ->numeric()
                        ->minValue(0)
                        ->prefix(fn (Get $get) => $get('currency') === 'UZS' ? 'soʻm' : null)
                        ->required(),

                    Forms\Components\Select::make('currency')
                        ->options([
                            'USD' => 'USD',
                            'EUR' => 'EUR',
                            'UZS' => 'UZS',
                        ])
                        ->default('USD')
                        ->native(false)
                        ->required(),

                    // 5) Validity window (optional)
                    Forms\Components\DatePicker::make('valid_from')
                        ->native(false),
                    Forms\Components\DatePicker::make('valid_to')
                        ->native(false)
                        ->afterOrEqual('valid_from'),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),

                    Forms\Components\Textarea::make('notes')
                        ->rows(2)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('rateItem.name')
                    ->label('Rate Item')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('rateItem.mode')
                    ->label('Mode')
                    ->badge()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('transportType.name')
                    ->label('Transport Type')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('unit')
                    ->label('Unit')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'flat' => 'Flat',
                        'per_hour' => 'Per hour',
                        'per_km' => 'Per km',
                        'per_ticket' => 'Per ticket',
                        null => 'Default',
                        default => $state,
                    })
                    ->badge(),

                Tables\Columns\TextColumn::make('amount')
                    ->money(fn ($record) => $record->currency, true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('valid_from')->date(),
                Tables\Columns\TextColumn::make('valid_to')->date(),

                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
                Tables\Columns\TextColumn::make('updated_at')->since()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rate_item_id')
                    ->label('Rate Item')
                    ->relationship('rateItem', 'name')
                    ->preload(),
                Tables\Filters\SelectFilter::make('transport_type_id')
                    ->label('Transport Type')
                    ->relationship('transportType', 'name')
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
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
            'index'  => Pages\ListTransportRates::route('/'),
            'create' => Pages\CreateTransportRate::route('/create'),
            'edit'   => Pages\EditTransportRate::route('/{record}/edit'),
        ];
    }
}
