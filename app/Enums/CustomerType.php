<?php

namespace App\Enums;

enum CustomerType: string
{
    case INDIVIDUAL = 'individual';
    case COMPANY = 'company';

    public function label(): string
    {
        return match ($this) {
            self::INDIVIDUAL => 'Individual',
            self::COMPANY => 'Company',
        };
    }

    public static function options(): array
    {
        return [
            self::INDIVIDUAL->value => self::INDIVIDUAL->label(),
            self::COMPANY->value => self::COMPANY->label(),
        ];
    }
}
