<?php

namespace App\Helper\EnumStatus;

enum DeviceStatus: int
{
    case ACTIVE = 1;
    case EXPIRED = 10;
    case BLOCKED = 11;
}