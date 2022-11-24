<?php

namespace App\Helper\EnumType;

enum PurchaseType: int
{
    case SCHEDULE = 1;

    public static function getTypes(): array
    {
        return [
            self::SCHEDULE->value
        ];
    }
}