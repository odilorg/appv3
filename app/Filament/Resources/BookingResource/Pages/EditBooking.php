<?php

// app/Filament/Resources/BookingResource/Pages/EditBooking.php

namespace App\Filament\Resources\BookingResource\Pages;

use Filament\Actions;
use App\Models\Booking;
use App\Services\BookingItinerarySync;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\BookingResource;

class EditBooking extends EditRecord
{
    protected static string $resource = BookingResource::class;

   protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('regenerateFromTour')
            ->label('Regenerate from Tour')
            ->icon('heroicon-o-arrow-path')
            ->form([
                \Filament\Forms\Components\Select::make('mode')
                    ->label('Mode')
                    ->native(false)
                    ->options([
                        'merge'   => 'Merge (keep custom & locked; update others)',
                        'replace' => 'Replace (wipe non-custom, unlocked; then rebuild)',
                    ])
                    ->default('merge')
                    ->required(),
            ])
            ->requiresConfirmation()
            ->visible(fn ($record) => filled($record->tour_id) && filled($record->start_date))
            ->action(function (array $data, $record) {
                $record->refreshDatesFromTrip();
                $record->saveQuietly();

                BookingItinerarySync::fromTripTemplate($record, $data['mode'] ?? 'merge');

                $record->refresh();
                $this->fillForm();
                $this->dispatch('refresh');

                Notification::make()
                    ->title('Itinerary synchronized ('.$data['mode'].').')
                    ->success()
                    ->send();
            }),
        ...parent::getHeaderActions(),
    ];
    }
}
