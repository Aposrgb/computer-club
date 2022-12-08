<?php

namespace App\Helper\Filter;

use App\Service\ValidatorService;
use Symfony\Component\Validator\Constraints as Assert;

class RoomFilter
{
    #[Assert\Callback(callback: [ValidatorService::class, 'validateDate'], groups: ['filter'])]
    private $startDate = null;

    #[Assert\Callback(callback: [ValidatorService::class, 'validateDate'], groups: ['filter'])]
    private $endDate = null;

    public function getStartDate(): mixed
    {
        return $this->startDate;
    }

    public function setStartDate($startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): mixed
    {
        return $this->endDate;
    }

    public function setEndDate($endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }


}