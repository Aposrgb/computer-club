<?php

namespace App\Helper\DTO;

use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class RoomDTO
{
    /** @OA\Property(type="integer") */
    #[Assert\NotBlank(groups: ['create_room'])]
    #[Assert\Positive(groups: ['create_room'])]
    #[Assert\Type(type: 'integer', groups: ['create_room'])]
    #[Groups(['create_room'])]
    private $typeId = null;


    /**
     * @return null
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * @param null $typeId
     * @return RoomDTO
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;
        return $this;
    }

}