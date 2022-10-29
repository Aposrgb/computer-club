<?php

namespace App\Helper\Mapper;

use App\Entity\EntityType;
use App\Helper\DTO\EntityTypeDTO;
use App\Helper\Interface\MapperInterface;
use App\Service\HelperService;

class EntityTypeMapper implements MapperInterface
{
    public function __construct(
        protected HelperService $helperService
    )
    {
    }

    /**
     * @param EntityTypeDTO $dto
     * @param EntityType|null $entity
     * @return EntityType
     */
    public function dtoToEntity($dto, $entity = null)
    {
        $entity = $entity ?? new EntityType();
        return $entity
            ->setPrice($this->helperService
                ->getActualValueNumber($entity->getPrice(), $dto->getPrice())
            )
            ->setDescription($this->helperService
                ->getActualValue($entity->getDescription(), $dto->getDescription())
            )
            ->setType($this->helperService
                ->getActualValueNumber($entity->getType(), $dto->getType())
            )
            ;

    }

    public function entityToDTO($entity)
    {
        // TODO: Implement entityToDTO() method.
    }

}