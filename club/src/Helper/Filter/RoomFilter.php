<?php

namespace App\Helper\Filter;

use App\Service\ValidatorService;
use Symfony\Component\Validator\Constraints as Assert;

class RoomFilter
{
    #[Assert\Callback(callback: [ValidatorService::class, 'validateDate'], groups: ['filter'])]
    private $date = null;

    #[Assert\Callback(callback: [ValidatorService::class, 'validateInteger'], groups: ['filter'])]
    private string|null|int $countHours = null;

    public function getDate(): mixed
    {
        return $this->date;
    }

    public function setDate(mixed $date): RoomFilter
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return int|string|null
     */
    public function getCountHours(): int|string|null
    {
        return $this->countHours;
    }

    /**
     * @param int|string|null $countHours
     * @return RoomFilter
     */
    public function setCountHours(int|string|null $countHours): RoomFilter
    {
        $this->countHours = $countHours;
        return $this;
    }


}