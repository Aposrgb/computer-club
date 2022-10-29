<?php

namespace App\Helper\DTO;

use App\Service\ValidatorService;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class ScheduleDTO
{
    /** @OA\Property(type="integer") */
    #[Assert\NotBlank(groups: ['create_schedule'])]
    #[Assert\Type(type: 'integer', groups: ['create_schedule'])]
    #[Assert\Range(min: 1, groups: ['create_schedule'])]
    #[Groups(groups: ['create_schedule'])]
    private $hours = null;

    /** @OA\Property(type="string") */
    #[Assert\NotBlank(groups: ['create_schedule'])]
    #[Assert\Callback(callback: [ValidatorService::class, 'validateDate'], groups: ['create_schedule'])]
    #[Groups(groups: ['create_schedule'])]
    private $dateStart = null;

    /** @OA\Property(type="integer") */
    #[Assert\NotBlank(groups: ['create_schedule'])]
    #[Assert\Type(type: 'integer', groups: ['create_schedule'])]
    #[Assert\Range(min: 1, groups: ['create_schedule'])]
    #[Groups(groups: ['create_schedule'])]
    private $computerId = null;

    /**
     * @return null
     */
    public function getHours()
    {
        return $this->hours;
    }

    /**
     * @param null $hours
     * @return ScheduleDTO
     */
    public function setHours($hours)
    {
        $this->hours = $hours;
        return $this;
    }

    /**
     * @return null
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * @param null $dateStart
     * @return ScheduleDTO
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;
        return $this;
    }

    /**
     * @return null
     */
    public function getComputerId()
    {
        return $this->computerId;
    }

    /**
     * @param null $computerId
     * @return ScheduleDTO
     */
    public function setComputerId($computerId)
    {
        $this->computerId = $computerId;
        return $this;
    }
}