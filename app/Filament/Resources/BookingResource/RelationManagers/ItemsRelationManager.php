<?php

namespace App\Filament\Resources\BookingResource\RelationManagers;

use App\Models\Car;
use Filament\Forms;
use Filament\Tables;
use App\Models\Guide;
use App\Models\Hotel;
use App\Models\Driver;
use App\Models\Vehicle;

// Supplier models – adjust to your app if names differ
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $recordTitleAttribute = 'title';
    protected static ?string $title = 'Itinerary';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('type')
                ->label('Type')
                ->native(false)
                ->options(['day' => 'Day', 'stop' => 'Stop'])
                ->required(),

            Forms\Components\DatePicker::make('date')
                ->label('Date')
                ->required(),

            Forms\Components\TextInput::make('title')
                ->label('Title')
                ->required()
                ->maxLength(255),

            Forms\Components\Textarea::make('description')
                ->label('Description')
                ->rows(3),

            Forms\Components\TimePicker::make('planned_start_time')
                ->label('Start')
                ->seconds(false),

            Forms\Components\TextInput::make('planned_duration_minutes')
                ->label('Dur (min)')
                ->numeric()
                ->minValue(0),

            Forms\Components\TextInput::make('sort_order')
                ->numeric()
                ->minValue(0)
                ->helperText('Tip: you can drag & drop rows to reorder.'),

            Forms\Components\Select::make('status')
                ->label('Status')
                ->native(false)
                ->options([
                    'planned'   => 'Planned',
                    'confirmed' => 'Confirmed',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ])
                ->default('planned'),

            Forms\Components\KeyValue::make('meta')
                ->label('Meta')
                ->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')     // drag & drop
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),

                // Tables\Columns\BadgeColumn::make('type')
                //     ->label('Type')
                //     ->colors([
                //         'info' => 'day',
                //         'gray' => 'stop',
                //     ]),

                Tables\Columns\TextColumn::make('title')
                    ->wrap()
                    ->limit(60)
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_custom')
                    ->boolean()
                    ->label('Custom'),

                Tables\Columns\IconColumn::make('is_locked')
                    ->boolean()
                    ->label('Locked'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('planned_start_time')
                    ->label('Start'),

                Tables\Columns\TextColumn::make('planned_duration_minutes')
                    ->label('Dur (min)'),

                Tables\Columns\TextColumn::make('assignments_count')
                    ->counts('assignments')
                    ->label('Suppliers')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'planned'   => 'Planned',
                        'confirmed' => 'Confirmed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->headerActions([
                // Create a CUSTOM item (independent of the Tour template)
                Tables\Actions\CreateAction::make()
                    ->label('Add Custom Item')
                    ->mutateFormDataUsing(function (array $data) {
                        $data['is_custom'] = true;
                        $data['tour_itinerary_item_id'] = null;
                        $data['status'] = $data['status'] ?? 'planned';
                        return $data;
                    }),

                // Bulk utility: shift all dates by N days
                Tables\Actions\Action::make('shiftDates')
                    ->label('Shift all dates')
                    ->icon('heroicon-o-calendar')
                    ->form([
                        Forms\Components\TextInput::make('days')
                            ->numeric()
                            ->required()
                            ->helperText('Positive to move later, negative to move earlier.'),
                    ])
                    ->action(function (array $data) {
                        $days = (int) ($data['days'] ?? 0);
                        if ($days !== 0) {
                            $booking = $this->getOwnerRecord(); // Booking
                            $booking->items()->get()->each(function ($item) use ($days) {
                                $item->date = $item->date->copy()->addDays($days);
                                $item->save();
                            });

                            Notification::make()->title('Dates shifted.')->success()->send();
                            $this->dispatch('refresh');
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                // Manage polymorphic assignments (Guide / Driver / Vehicle / Hotel)
                Tables\Actions\Action::make('manageAssignments')
                    ->label('Assignments')
                    ->icon('heroicon-o-users')
                    ->modalHeading('Manage Assignments')
                    ->modalWidth('5xl')
                    ->form([
                        Forms\Components\Repeater::make('assignments')
                            ->label('Suppliers for this item')
                            ->columns(3)
                            ->addActionLabel('Add assignment')
                            ->reorderable(true)
                            ->schema([
                               Forms\Components\Select::make('assignable_type')
        ->label('Type')
        ->native(false)
        ->required()
        ->options([
            Guide::class  => 'Guide',
            Driver::class => 'Driver',
            // Hotel::class  => 'Hotel',
        ])
        ->live(),

    Forms\Components\Select::make('assignable_id')
        ->label('Supplier')
        ->native(false)
        ->required()
        ->options(function (Get $get) {
            return match ($get('assignable_type')) {
                Guide::class  => Guide::query()->orderBy('name')->pluck('name', 'id'),
                Driver::class => Driver::query()->orderBy('name')->pluck('name', 'id'),
                default       => collect(),
            };
        })
        ->searchable()
        ->live()
        ->afterStateUpdated(function (Forms\Set $set, Get $get) {
            // if the type is Driver and the driver changed, clear car_id
            if ($get('assignable_type') === Driver::class) {
                $set('car_id', null);
            }
        }),

    // Car (visible only when Type = Driver)
    Forms\Components\Select::make('car_id')
        ->label('Car')
        ->native(false)
        ->searchable()
        ->visible(fn (Get $get) => $get('assignable_type') === Driver::class)
        ->options(function (Get $get) {
            if ($get('assignable_type') !== Driver::class) {
                return collect();
            }
            $driverId = $get('assignable_id');
            if (! $driverId) {
                return collect();
            }
            return Car::query()
                ->where('driver_id', $driverId)
                ->orderBy('plate_number')
                ->get()
                ->mapWithKeys(fn (Car $c) => [
                    $c->id => "{$c->plate_number} — {$c->make} {$c->model}",
                ]);
        })
        ->helperText('Choose a car owned by the selected driver.')
        ->rule(function (Get $get) {
            // Server-side guard: when Driver is selected, car must belong to that driver
            if ($get('assignable_type') !== Driver::class) {
                return null; // no rule in other cases
            }
            $driverId = $get('assignable_id');
            return \Illuminate\Validation\Rule::exists('cars', 'id')
                ->where(fn ($q) => $q->where('driver_id', $driverId));
        }),

                                Forms\Components\TextInput::make('role')
                                    ->label('Role / Service')
                                    ->placeholder('guide / driver / vehicle / hotel / other'),

                                Forms\Components\TextInput::make('quantity')
                                    ->label('Qty')
                                    ->numeric()
                                    ->minValue(0),

                                Forms\Components\TextInput::make('cost')
                                    ->label('Cost')
                                    ->numeric(),

                                Forms\Components\TextInput::make('currency')
                                    ->label('Cur')
                                    ->maxLength(3)
                                    ->default('USD'),

                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->native(false)
                                    ->options([
                                        'planned'   => 'Planned',
                                        'confirmed' => 'Confirmed',
                                        'completed' => 'Completed',
                                        'cancelled' => 'Cancelled',
                                    ]),

                                Forms\Components\TimePicker::make('start_time')
                                    ->label('Start')
                                    ->seconds(false),

                                Forms\Components\TimePicker::make('end_time')
                                    ->label('End')
                                    ->seconds(false),

                                Forms\Components\Textarea::make('notes')
                                    ->label('Notes')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    // Prefill modal with current assignments
                    ->mountUsing(function (Form $form, $record) {
                        $form->fill([
                            'assignments' => $record->assignments()
                                ->get()
                                ->map(fn ($a) => [
                                    'assignable_type' => $a->assignable_type,
                                    'assignable_id'   => $a->assignable_id,
                                    'role'            => $a->role,
                                    'quantity'        => $a->quantity,
                                    'cost'            => $a->cost,
                                    'currency'        => $a->currency,
                                    'status'          => $a->status,
                                    'start_time'      => $a->start_time,
                                    'end_time'        => $a->end_time,
                                    'notes'           => $a->notes,
                                ])
                                ->toArray(),
                        ]);
                    })
                    // Persist: simple sync (delete & recreate)
                    ->action(function (array $data, $record) {
                        /** @var \App\Models\BookingItineraryItem $record */
                        $record->assignments()->delete();

                        foreach (($data['assignments'] ?? []) as $row) {
                            if (!empty($row['assignable_type']) && !empty($row['assignable_id'])) {
                                $record->assignments()->create([
                                    'assignable_type' => $row['assignable_type'],
                                    'assignable_id'   => $row['assignable_id'],
                                    'role'            => $row['role']        ?? null,
                                    'quantity'        => $row['quantity']    ?? null,
                                    'cost'            => $row['cost']        ?? null,
                                    'currency'        => $row['currency']    ?? 'USD',
                                    'status'          => $row['status']      ?? null,
                                    'start_time'      => $row['start_time']  ?? null,
                                    'end_time'        => $row['end_time']    ?? null,
                                    'notes'           => $row['notes']       ?? null,
                                ]);
                            }
                        }

                        Notification::make()->title('Assignments saved.')->success()->send();
                        $this->dispatch('refresh');
                    }),

                // Quick lock/unlock to protect from regen overwrites
                Tables\Actions\Action::make('toggleLock')
                    ->label(fn ($record) => $record->is_locked ? 'Unlock' : 'Lock')
                    ->icon('heroicon-o-lock-closed')
                    ->color(fn ($record) => $record->is_locked ? 'gray' : 'warning')
                    ->action(function ($record) {
                        $record->is_locked = ! $record->is_locked;
                        $record->save();

                        Notification::make()
                            ->title('Item ' . ($record->is_locked ? 'locked' : 'unlocked') . '.')
                            ->success()
                            ->send();

                        $this->dispatch('refresh');
                    }),

                // Cancel without deleting (keeps audit trail)
                Tables\Actions\Action::make('cancelItem')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn ($record) => $record->status !== 'cancelled')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->status = 'cancelled';
                        $record->save();

                        Notification::make()->title('Item cancelled.')->success()->send();
                        $this->dispatch('refresh');
                    }),

                // Soft delete (because model uses SoftDeletes)
                Tables\Actions\DeleteAction::make()
                    ->successNotificationTitle('Item removed'),
            ])
            ->bulkActions([])   // keep simple
            ->paginated(false);
    }

    /**
     * Ensure newly created rows are CUSTOM and attached to this booking.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        /** @var \App\Models\Booking $booking */
        $booking = $this->getOwnerRecord();

        $max = $booking->items()->max('sort_order');

        $data['booking_id'] = $booking->id;
        $data['sort_order'] = is_null($max) ? 0 : $max + 1;

        // Custom item flags
        $data['is_custom'] = true;
        $data['tour_itinerary_item_id'] = null;

        // Default status if not set
        $data['status'] = $data['status'] ?? 'planned';

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // No special changes needed; keep user edits
        return $data;
    }
}
