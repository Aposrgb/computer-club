<?php

namespace App\Helper\EnumStatus;

enum ComputerStatus: int
{
    case ACTIVE = 1;
    case ON_SERVICE = 2;
}