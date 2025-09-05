<?php

namespace App\Filament\Resources\BookingResource\RelationManagers;

use App\Models\Car;
use Filament\Forms;
use App\Models\Room;
use Filament\Tables;
use App\Models\Guide;
use App\Models\Hotel;
use App\Models\Driver;
use App\Models\Vehicle;

// Supplier models – adjust to your app if names differ
use Filament\Forms\Get;
use Filament\Forms\Form;
use App\Models\Restaurant;
use Filament\Tables\Table;
use App\Models\RestaurantMeal;
use Illuminate\Validation\Rule;
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

            // Forms\Components\TextInput::make('sort_order')
            //     ->numeric()
            //     ->minValue(0)
            //     ->helperText('Tip: you can drag & drop rows to reorder.'),

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
            // ->reorderable('sort_order')
            // ->defaultSort('sort_order')
            ->columns([
                // Tables\Columns\TextColumn::make('sort_order')->label('#')->sortable(),

                Tables\Columns\TextColumn::make('date')->date(),
                // Tables\Columns\BadgeColumn::make('type')->label('Type')->colors(['info' => 'day', 'gray' => 'stop']),

                Tables\Columns\TextColumn::make('title')
                    ->wrap()
                    ->limit(60)
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_custom')->boolean()->label('Custom'),
                Tables\Columns\IconColumn::make('is_locked')->boolean()->label('Locked'),

                Tables\Columns\TextColumn::make('status')->label('Status')->toggleable(),
                Tables\Columns\TextColumn::make('planned_start_time')->label('Start'),
                Tables\Columns\TextColumn::make('planned_duration_minutes')->label('Dur (min)'),

                Tables\Columns\TextColumn::make('assignments_count')
                    ->counts('assignments')
                    ->label('Suppliers'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'planned'   => 'Planned',
                    'confirmed' => 'Confirmed',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Custom Item')
                    ->mutateFormDataUsing(function (array $data) {
                        $data['is_custom'] = true;
                        $data['tour_itinerary_item_id'] = null;
                        $data['status'] = $data['status'] ?? 'planned';
                        return $data;
                    }),

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
                                        \App\Models\Guide::class      => 'Guide',
                                        \App\Models\Driver::class     => 'Driver',
                                        \App\Models\Hotel::class      => 'Hotel',
                                        \App\Models\Restaurant::class => 'Restaurant',
                                    ])
                                    ->live(),

                                Forms\Components\Select::make('assignable_id')
                                    ->label('Supplier')
                                    ->native(false)
                                    ->required()
                                    ->options(function (Forms\Get $get) {
                                        return match ($get('assignable_type')) {
                                            \App\Models\Guide::class      => \App\Models\Guide::query()->orderBy('name')->pluck('name', 'id')->all(),
                                            \App\Models\Driver::class     => \App\Models\Driver::query()->orderBy('name')->pluck('name', 'id')->all(),
                                            \App\Models\Hotel::class      => \App\Models\Hotel::query()->orderBy('name')->pluck('name', 'id')->all(),
                                            \App\Models\Restaurant::class => \App\Models\Restaurant::query()->orderBy('name')->pluck('name', 'id')->all(),
                                            default                       => [],
                                        };
                                    })
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                        if ($get('assignable_type') === \App\Models\Driver::class) {
                                            $set('car_id', null); // reset car when driver changes
                                        }
                                    }),

                                // CAR (Driver only) — scalar select
                                Forms\Components\Select::make('car_id')
                                    ->label('Car')
                                    ->native(false)
                                    ->searchable()
                                    ->multiple(false)
                                    ->preload()
                                    ->visible(fn (Forms\Get $get) => $get('assignable_type') === \App\Models\Driver::class)
                                    ->options(function (Forms\Get $get) {
                                        if ($get('assignable_type') !== \App\Models\Driver::class) {
                                            return [];
                                        }
                                        $driverId = (int) ($get('assignable_id') ?? 0);
                                        if (! $driverId) {
                                            return [];
                                        }
                                        return \App\Models\Car::query()
                                            ->where('driver_id', $driverId)
                                            ->orderBy('plate_number')
                                            ->get()
                                            ->mapWithKeys(fn(\App\Models\Car $c) => [
                                                $c->id => "{$c->plate_number} — {$c->make} {$c->model}",
                                            ])
                                            ->all();
                                    })
                                    ->rule(function (Forms\Get $get) {
                                        if ($get('assignable_type') !== \App\Models\Driver::class) {
                                            return null;
                                        }
                                        $driverId = (int) ($get('assignable_id') ?? 0);
                                        return \Illuminate\Validation\Rule::exists('cars', 'id')
                                            ->where(fn($q) => $q->where('driver_id', $driverId));
                                    }),

                                // -------- HOTEL: MULTIPLE ROOMS --------
                                Forms\Components\Section::make('Rooms')
                                    ->visible(fn (Forms\Get $get) => $get('assignable_type') === \App\Models\Hotel::class)
                                    ->columnSpanFull()
                                    ->collapsible()
                                    ->schema([
                                        Forms\Components\Repeater::make('rooms')
                                            ->columns(3)
                                            ->addActionLabel('Add room')
                                            ->schema([
                                                Forms\Components\Select::make('room_id')
                                                    ->label('Room type')
                                                    ->native(false)
                                                    ->searchable()
                                                    ->required()
                                                    ->options(function (Forms\Get $get) {
                                                        $hotelId = (int) ($get('../../assignable_id') ?? 0);
                                                        if (! $hotelId) return [];
                                                        return \App\Models\Room::query()
                                                            ->where('hotel_id', $hotelId)
                                                            ->orderBy('name')
                                                            ->pluck('name', 'id')
                                                            ->all();
                                                    })
                                                    ->rule(function (Forms\Get $get) {
                                                        $hotelId = (int) ($get('../../assignable_id') ?? 0);
                                                        if (! $hotelId) return null;
                                                        return \Illuminate\Validation\Rule::exists('rooms', 'id')
                                                            ->where(fn ($q) => $q->where('hotel_id', $hotelId));
                                                    })
                                                    ->reactive(),

                                                Forms\Components\TextInput::make('quantity')
                                                    ->label('Qty')
                                                    ->numeric()
                                                    ->minValue(1)
                                                    ->default(1)
                                                    ->required(),

                                                Forms\Components\TextInput::make('notes')
                                                    ->label('Notes')
                                                    ->maxLength(255)
                                                    ->columnSpanFull(),
                                            ]),
                                    ]),

                                // MEAL (Restaurant only)
                                Forms\Components\Select::make('restaurant_meal_id')
                                    ->label('Meal')
                                    ->native(false)
                                    ->searchable()
                                    ->visible(fn (Forms\Get $get) => $get('assignable_type') === \App\Models\Restaurant::class)
                                    ->options(function (Forms\Get $get) {
                                        if ($get('assignable_type') !== \App\Models\Restaurant::class) return [];
                                        $restaurantId = (int) ($get('assignable_id') ?? 0);
                                        if (! $restaurantId) return [];
                                        return \App\Models\RestaurantMeal::query()
                                            ->where('restaurant_id', $restaurantId)
                                            ->where('is_active', true)
                                            ->get()
                                            ->mapWithKeys(fn (\App\Models\RestaurantMeal $m) => [
                                                $m->id => $m->name . ' — ' . number_format((float) $m->price, 2) . ' ' . $m->currency . ($m->per_person ? ' /pp' : ''),
                                            ])
                                            ->all();
                                    })
                                    ->live()
                                    ->afterStateUpdated(function () {
                                        // no-op
                                    }),

                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->native(false)
                                    ->options([
                                        'planned'   => 'Planned',
                                        'confirmed' => 'Confirmed',
                                        'completed' => 'Completed',
                                        'cancelled' => 'Cancelled',
                                    ]),

                                Forms\Components\TimePicker::make('start_time')->label('Start')->seconds(false),
                                Forms\Components\TimePicker::make('end_time')->label('End')->seconds(false),

                                Forms\Components\Textarea::make('notes')
                                    ->label('Notes')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->mountUsing(function (Forms\Form $form, $record) {
                        // Load assignments from DB
                        $raw = $record->assignments()->get();

                        // HOTEL rows: group and flatten into rooms[]
                        $hotelsGrouped = $raw
                            ->where('assignable_type', \App\Models\Hotel::class)
                            ->groupBy('assignable_id')
                            ->map(function ($group, $hotelId) {
                                return [
                                    'assignable_type' => (string) \App\Models\Hotel::class,
                                    'assignable_id'   => (int) $hotelId,
                                    'rooms' => $group->map(function ($a) {
                                        return [
                                            'room_id'  => $a->room_id ? (int) $a->room_id : null,
                                            'quantity' => $a->quantity ? (int) $a->quantity : 1,
                                            'notes'    => $a->notes ?: null,
                                        ];
                                    })->filter(fn($r) => !empty($r['room_id']))->values()->all(),
                                    'status'     => $group->firstWhere('status', '!=', null)->status ?? null,
                                    'start_time' => $group->firstWhere('start_time', '!=', null)->start_time ?? null,
                                    'end_time'   => $group->firstWhere('end_time', '!=', null)->end_time ?? null,
                                    'notes'      => $group->firstWhere('notes', '!=', null)->notes ?? null,
                                ];
                            })
                            ->values()
                            ->all(); // <-- turn into plain array

                        // Non-hotel rows 1:1 (NO rooms key)
                        $others = $raw
                            ->where('assignable_type', '!=', \App\Models\Hotel::class)
                            ->map(function ($a) {
                                return [
                                    'assignable_type'    => (string) $a->assignable_type,
                                    'assignable_id'      => (int) $a->assignable_id,
                                    'car_id'             => $a->car_id ? (int) $a->car_id : null,
                                    'restaurant_meal_id' => $a->restaurant_meal_id ? (int) $a->restaurant_meal_id : null,
                                    'status'             => $a->status ?: null,
                                    'start_time'         => $a->start_time ?: null,
                                    'end_time'           => $a->end_time ?: null,
                                    'notes'              => $a->notes ?: null,
                                ];
                            })
                            ->values()
                            ->all(); // <-- plain array

                        // IMPORTANT: merge as plain arrays to avoid Eloquent\Collection::merge (which expects models)
                        $merged = array_merge($hotelsGrouped, $others);

                        $form->fill([
                            'assignments' => $merged,
                        ]);
                    })
                    ->action(function (array $data, $record) {
                        /** @var \App\Models\BookingItineraryItem $record */
                        $record->assignments()->delete();

                        foreach (($data['assignments'] ?? []) as $row) {
                            $type = $row['assignable_type'] ?? null;
                            $assignableId = isset($row['assignable_id']) ? (int) $row['assignable_id'] : null;
                            if (!$type || !$assignableId) {
                                continue;
                            }

                            if ($type === \App\Models\Hotel::class) {
                                $rooms = $row['rooms'] ?? [];
                                if (!is_array($rooms) || empty($rooms)) {
                                    continue;
                                }
                                foreach ($rooms as $room) {
                                    $roomId = isset($room['room_id']) ? (int) $room['room_id'] : null;
                                    if (!$roomId) continue;

                                    $record->assignments()->create([
                                        'assignable_type' => (string) $type,
                                        'assignable_id'   => $assignableId,
                                        'room_id'         => $roomId,
                                        'quantity'        => isset($room['quantity']) ? (int) $room['quantity'] : 1,
                                        'status'          => $row['status']     ?? null,
                                        'start_time'      => $row['start_time'] ?? null,
                                        'end_time'        => $row['end_time']   ?? null,
                                        'notes'           => $room['notes']      ?? ($row['notes'] ?? null),
                                    ]);
                                }
                                continue;
                            }

                            // Non-hotel 1:1
                            $record->assignments()->create([
                                'assignable_type'    => (string) $type,
                                'assignable_id'      => $assignableId,
                                'car_id'             => isset($row['car_id']) ? (int) $row['car_id'] : null,
                                'restaurant_meal_id' => isset($row['restaurant_meal_id']) ? (int) $row['restaurant_meal_id'] : null,
                                'status'             => $row['status']     ?? null,
                                'start_time'         => $row['start_time'] ?? null,
                                'end_time'           => $row['end_time']   ?? null,
                                'notes'              => $row['notes']      ?? null,
                            ]);
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Assignments saved.')
                            ->success()
                            ->send();

                        $this->dispatch('refresh');
                    }),

                // Quick lock/unlock to protect from regen overwrites
                Tables\Actions\Action::make('toggleLock')
                    ->label(fn($record) => $record->is_locked ? 'Unlock' : 'Lock')
                    ->icon('heroicon-o-lock-closed')
                    ->color(fn($record) => $record->is_locked ? 'gray' : 'warning')
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
                    ->visible(fn($record) => $record->status !== 'cancelled')
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
            ->bulkActions([])
            ->paginated(false);
    }

    /**
     * Ensure newly created rows are CUSTOM and attached to this booking.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        /** @var \App\Models\Booking $booking */
        $booking = $this->getOwnerRecord();

        $data['booking_id'] = $booking->id;
        $data['is_custom'] = true;
        $data['tour_itinerary_item_id'] = null;
        $data['status'] = $data['status'] ?? 'planned';

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // No special changes needed; keep user edits
        return $data;
    }
}
