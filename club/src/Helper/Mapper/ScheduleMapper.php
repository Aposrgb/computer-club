<?php

namespace App\Helper\Mapper;

use App\Entity\Schedule;
use App\Helper\DTO\ScheduleDTO;
use App\Helper\Interface\MapperInterface;
use App\Service\HelperService;

class ScheduleMapper implements MapperInterface
{
    public function __construct(
        protected HelperService $helperService
    )
    {
    }

    /**
     * @param ScheduleDTO $dto
     * @param Schedule $entity
     */
    public function dtoToEntity($dto, $entity = null)
    {
        $entity = $entity ?? new Schedule();
        return $entity
            ->setDateStart($this->helperService->getActualValueDate($entity->getDateStart(), $dto->getDateStart()))
            ->setDateEnd($this->helperService->getActualValueDate($entity->getDateEnd(), $dto->getDateEnd()));
    }

    public function entityToDTO($entity)
    {
        // TODO: Implement entityToDTO() method.
    }
}