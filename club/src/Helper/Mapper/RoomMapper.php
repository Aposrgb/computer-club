<?php

namespace App\Helper\Mapper;

use App\Entity\Room;
use App\Helper\DTO\RoomDTO;
use App\Helper\Interface\MapperInterface;
use App\Service\HelperService;

class RoomMapper implements MapperInterface
{
    public function __construct(
        protected HelperService $helperService
    )
    {
    }

    /**
     * @param RoomDTO $dto
     * @param Room|null $entity
     * @return Room
     */
    public function dtoToEntity($dto, $entity = null)
    {
        $entity = $entity ?? new Room();
        return $entity
            ->setMaxCountComputers($this->helperService
                ->getActualValueNumber($entity->getMaxCountComputers(), $dto->getMaxCountComputers())
            );

    }

    public function entityToDTO($entity)
    {
        // TODO: Implement entityToDTO() method.
    }

}