<?php

namespace App\Helper\Mapper;

use App\Entity\User;
use App\Helper\DTO\UserDTO;
use App\Helper\Interface\MapperInterface;
use App\Helper\Trait\HelperTrait;

class UserMapper implements MapperInterface
{
    use HelperTrait;

    /**
     * @param UserDTO $dto
     * @param User $entity
     */
    public function dtoToEntity($dto, $entity = null)
    {
        $entity = $entity ?? new User();
        return $entity
            ->setName($this->getActualValue($entity->getName(), $dto->getName()))
            ->setPhone($this->getActualValue($entity->getPhone(), $dto->getPhone()))
            ->setEmail($this->getActualValue($entity->getEmail(), $dto->getEmail()))
            ->setSurname($this->getActualValue($entity->getSurname(), $dto->getSurname()));
    }

    public function entityToDTO($entity)
    {
        // TODO: Implement entityToDTO() method.
    }

}