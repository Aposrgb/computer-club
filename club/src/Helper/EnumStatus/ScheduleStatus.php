<?php

namespace App\Helper\EnumStatus;

enum ScheduleStatus: int
{
    case WAIT_PAYMENT = 0;
    case ACTIVE = 1;
    case ARCHIVE = 2;
    case CANCELLED = 3;

    public static function getStatus(): array
    {
        return [
            'Ожидание оплаты' => self::WAIT_PAYMENT->value,
            'Активный' => self::ACTIVE->value,
            'В архиве' => self::ARCHIVE->value,
            'Отменен' => self::CANCELLED->value,
        ];
    }
}