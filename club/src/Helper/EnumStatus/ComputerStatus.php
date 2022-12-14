<?php

namespace App\Helper\EnumStatus;

enum ComputerStatus: int
{
    case ACTIVE = 1;
    case ON_SERVICE = 2;
    case NOT_SELECT_DATE = 3;
    case ARCHIVE = 10;
}