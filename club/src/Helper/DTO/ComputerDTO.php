<?php

namespace App\Helper\DTO;

use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class ComputerDTO
{
    /** @OA\Property(type="integer") */
    #[Assert\NotBlank(groups: ['create_computer'])]
    #[Assert\Positive(groups: ['create_computer'])]
    #[Assert\Type(type: 'integer', groups: ['create_computer'])]
    #[Groups(['create_computer'])]
    private $roomId = null;

    /** @OA\Property(type="integer") */
    #[Assert\NotBlank(groups: ['create_computer'])]
    #[Assert\Positive(groups: ['create_computer'])]
    #[Assert\Type(type: 'integer', groups: ['create_computer'])]
    #[Groups(['create_computer'])]
    private $typeId = null;

    /**
     * @return null
     */
    public function getRoomId()
    {
        return $this->roomId;
    }

    /**
     * @param null $roomId
     * @return ComputerDTO
     */
    public function setRoomId($roomId)
    {
        $this->roomId = $roomId;
        return $this;
    }

    /**
     * @return null
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * @param null $typeId
     * @return ComputerDTO
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;
        return $this;
    }

}