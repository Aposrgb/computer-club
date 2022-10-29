<?php

namespace App\Helper\EnumType;

enum EntityType: int
{
    case COMPUTER = 1;
    case ROOM = 2;

    public static function getTypes():array
    {
        return [
            ''.self::COMPUTER->value,
            ''.self::ROOM->value
        ];
    }
}