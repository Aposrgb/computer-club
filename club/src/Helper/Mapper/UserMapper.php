<?php

namespace App\Helper\Mapper;

use App\Entity\User;
use App\Helper\DTO\UserDTO;
use App\Helper\Interface\MapperInterface;
use App\Service\HelperService;

class UserMapper implements MapperInterface
{
    public function __construct(
        protected HelperService $helperService
    )
    {
    }

    /**
     * @param UserDTO $dto
     * @param User $entity
     */
    public function dtoToEntity($dto, $entity = null)
    {
        $entity = $entity ?? new User();
        return $entity
            ->setName($this->helperService->getActualValue($entity->getName(), $dto->getName()))
            ->setPhone($this->helperService->getActualValue($entity->getPhone(), $dto->getPhone()))
            ->setEmail($this->helperService->getActualValue($entity->getEmail(), $dto->getEmail()))
            ->setSurname($this->helperService->getActualValue($entity->getSurname(), $dto->getSurname()));
    }

    public function entityToDTO($entity)
    {
        // TODO: Implement entityToDTO() method.
    }

}