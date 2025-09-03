<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class BookingItinerarySync
{
    public static function fromTripTemplate(Booking $booking): void
    {
        if (! $booking->start_date) return;

        $tour = $booking->tour()
            ->with(['itineraryItems' => fn($q) => $q->with('parent')->orderBy('sort_order')->orderBy('id')])
            ->first();

        if (! $tour) return;

        DB::transaction(function () use ($booking, $tour) {
            // Optional: clean snapshot
            $booking->items()->delete();

            // Precompute day offsets: index top-level 'day' items by their sort sequence
            $dayOffsets = [];
            $dayIndex = 0;
            foreach ($tour->itineraryItems as $it) {
                if ($it->type === 'day' && $it->parent_id === null) {
                    $dayOffsets[$it->id] = $dayIndex++;
                }
            }

            foreach ($tour->itineraryItems as $item) {
                $dayOffset = self::offsetFor($item, $dayOffsets);

                $booking->items()->create([
                    'tour_itinerary_item_id'   => $item->id,
                    'date'                     => $booking->start_date->copy()->addDays($dayOffset),
                    'type'                     => $item->type,
                    'sort_order'               => $item->sort_order,
                    'title'                    => $item->title,
                    'description'              => $item->description,
                    'planned_start_time'       => $item->default_start_time,
                    'planned_duration_minutes' => $item->duration_minutes,
                    'meta'                     => $item->meta,
                ]);
            }
        });
    }

    protected static function offsetFor($item, array $dayOffsets): int
    {
        if ($item->type === 'day' && $item->parent_id === null) {
            return $dayOffsets[$item->id] ?? max(0, (int)$item->sort_order);
        }
        if ($item->parent && $item->parent->type === 'day') {
            return $dayOffsets[$item->parent->id] ?? 0;
        }
        // Fallback: flat layout → use sort_order as pseudo day
        return max(0, (int)$item->sort_order);
    }
}
