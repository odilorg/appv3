<?php

// app/Observers/BookingObserver.php

namespace App\Observers;

use App\Models\Booking;
use App\Services\BookingItinerarySync;

class BookingObserver
{
    public function created(Booking $booking): void
    {
        // Fresh record with ID present → generate snapshot
        if ($booking->tour_id && $booking->start_date) {
            BookingItinerarySync::fromTripTemplate($booking);
        }
    }

    public function updated(Booking $booking): void
    {
        // If tour or start_date changed → recompute end_date and regenerate items
        if ($booking->wasChanged(['tour_id', 'start_date'])) {
            // end_date is already maintained by saving() hook,
            // but ensure no extra events chatter:
            $booking->refreshDatesFromTrip();
            $booking->saveQuietly();

            BookingItinerarySync::fromTripTemplate($booking);
        }
    }
}
