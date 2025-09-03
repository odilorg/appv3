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
                ->requiresConfirmation()
                ->visible(fn (Booking $record) => filled($record->tour_id) && filled($record->start_date))
                ->action(function (Booking $record) {
                    // Recompute dates, save quietly
                    $record->refreshDatesFromTrip();
                    $record->saveQuietly();

                    // Rebuild snapshot
                    BookingItinerarySync::fromTripTemplate($record);

                    // Ensure Livewire has the latest model
                    $record->refresh();

                    // Refill the entire form from the fresh record
                    $this->fillForm();

                    // Ask children (like relation managers) to refresh
                    $this->dispatch('refresh');

                    Notification::make()
                        ->title('Itinerary regenerated from tour template.')
                        ->success()
                        ->send();
                }),
            ...parent::getHeaderActions(),
        ];
    }
}
