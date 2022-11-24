<?php

namespace App\Helper\EnumStatus;

enum ScheduleStatus: int
{
    case WAIT_PAYMENT = 0;
    case ACTIVE = 1;
    case ARCHIVE = 2;
    case CANCELLED = 3;
}