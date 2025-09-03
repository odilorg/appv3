<?php
namespace App\Services;

use App\Models\Booking;
use App\Models\BookingItineraryItem;
use Illuminate\Support\Facades\DB;

class BookingItinerarySync
{
    public static function fromTripTemplate(Booking $booking, string $mode = 'merge'): void
    {
        if (! $booking->start_date || ! $booking->tour_id) return;

        $tour = $booking->tour()
            ->with(['itineraryItems' => fn($q) => $q->with('parent')->orderBy('sort_order')->orderBy('id')])
            ->first();

        if (! $tour) return;

        DB::transaction(function () use ($booking, $tour, $mode) {
            // Precompute day offsets
            $dayOffsets = [];
            $dayIndex = 0;
            foreach ($tour->itineraryItems as $it) {
                if ($it->type === 'day' && $it->parent_id === null) {
                    $dayOffsets[$it->id] = $dayIndex++;
                }
            }

            // Index existing booking items by source tour item id
            $existing = $booking->items()->get()->keyBy('tour_itinerary_item_id');

            // If "replace": remove all NON-custom & UNLOCKED items first
            if ($mode === 'replace') {
                $booking->items()
                    ->where('is_custom', false)
                    ->where('is_locked', false)
                    ->delete(); // soft delete
                $existing = $booking->items()->get()->keyBy('tour_itinerary_item_id'); // refresh
            }

            // Upsert from tour
            foreach ($tour->itineraryItems as $src) {
                $date = $booking->start_date->copy()->addDays(self::offsetFor($src, $dayOffsets));
                /** @var BookingItineraryItem|null $row */
                $row = $existing->get($src->id);

                if ($row) {
                    // Only update if NOT locked and NOT custom
                    if (! $row->is_locked && ! $row->is_custom) {
                        $row->fill([
                            'date'                     => $date,
                            'type'                     => $src->type,
                            'sort_order'               => $src->sort_order,
                            'title'                    => $src->title,
                            'description'              => $src->description,
                            'planned_start_time'       => $src->default_start_time,
                            'planned_duration_minutes' => $src->duration_minutes,
                            'meta'                     => $src->meta,
                        ])->save();
                    }
                } else {
                    // New source item → create booking item (non-custom)
                    $booking->items()->create([
                        'tour_itinerary_item_id'   => $src->id,
                        'is_custom'                => false,
                        'is_locked'                => false,
                        'status'                   => 'planned',
                        'date'                     => $date,
                        'type'                     => $src->type,
                        'sort_order'               => $src->sort_order,
                        'title'                    => $src->title,
                        'description'              => $src->description,
                        'planned_start_time'       => $src->default_start_time,
                        'planned_duration_minutes' => $src->duration_minutes,
                        'meta'                     => $src->meta,
                    ]);
                }
            }

            // Remove non-custom, unlocked items whose source was removed (merge mode)
            if ($mode === 'merge') {
                $tourIds = $tour->itineraryItems->pluck('id')->all();
                $booking->items()
                    ->where('is_custom', false)
                    ->where('is_locked', false)
                    ->whereNotIn('tour_itinerary_item_id', $tourIds)
                    ->delete();
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
        return max(0, (int)$item->sort_order);
    }
}
