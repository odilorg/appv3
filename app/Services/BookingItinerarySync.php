<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\ItineraryItem;

class BookingItinerarySync
{
    public static function fromTripTemplate(Booking $booking): void
    {
        $trip = $booking->trip()->with(['itineraryItems' => function ($q) {
            $q->orderBy('sort_order');
        }])->first();

        if (!$trip) return;

        // Optional: wipe existing snapshot if re-generating
        $booking->items()->delete();

        // Compute base date per item. For hierarchy, compute offsets by walking the tree.
        $items = $trip->itineraryItems;
        foreach ($items as $item) {
            // Calculate day offset:
            // If you use Days as top level: offset by (day_index)
            // If flat: derive from sort_order or meta.day_index
            $dayOffset = self::offsetFor($item); // implement your own logic

            $booking->items()->create([
                'trip_itinerary_item_id'   => $item->id,
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
    }

    protected static function offsetFor(ItineraryItem $item): int
    {
        // Simple approach:
        // - if type == 'day' → use its sequence index (0-based) from top-level days
        // - if type == 'stop' → same as its parent day offset
        if ($item->type === 'day') {
            return max(0, $item->sort_order);
        }
        return $item->parent ? max(0, $item->parent->sort_order) : 0;
    }
}
