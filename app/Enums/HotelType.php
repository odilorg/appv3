<?php

namespace App\Enums;

enum HotelType: string
{
    case HOTEL      = 'hotel';
    case GUESTHOUSE = 'guesthouse';
    case HOSTEL     = 'hostel';
    case BOUTIQUE   = 'boutique';
    case RESORT     = 'resort';
    case APARTMENT  = 'apartment';
    case YURT_CAMP  = 'yurt_camp';

    public function label(): string
    {
        return match ($this) {
            self::HOTEL      => 'Hotel',
            self::GUESTHOUSE => 'Guesthouse',
            self::HOSTEL     => 'Hostel',
            self::BOUTIQUE   => 'Boutique Hotel',
            self::RESORT     => 'Resort',
            self::APARTMENT  => 'Apartment',
            self::YURT_CAMP  => 'Yurt Camp',
        };
    }

    public static function options(): array
    {
        return [
            self::HOTEL->value      => self::HOTEL->label(),
            self::GUESTHOUSE->value => self::GUESTHOUSE->label(),
            self::HOSTEL->value     => self::HOSTEL->label(),
            self::BOUTIQUE->value   => self::BOUTIQUE->label(),
            self::RESORT->value     => self::RESORT->label(),
            self::APARTMENT->value  => self::APARTMENT->label(),
            self::YURT_CAMP->value  => self::YURT_CAMP->label(),
        ];
    }
}
