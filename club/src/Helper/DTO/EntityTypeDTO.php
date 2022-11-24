<?php

namespace App\Helper\DTO;

use App\Helper\EnumType\EntityType;
use App\Service\ValidatorService;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class EntityTypeDTO
{
    /** @OA\Property(type="integer") */
    #[Assert\NotBlank(groups: ['create_computer'])]
    #[Assert\Callback(callback: [ValidatorService::class, 'validateInteger'], groups: ['create_type'])]
    #[Groups(['create_type'])]
    private $price = null;

    /** @OA\Property(type="string") */
    #[Assert\NotBlank(groups: ['create_computer'])]
    #[Assert\Length(max: 300, groups: ['create_type'])]
    #[Assert\Type(type: 'string', groups: ['create_type'])]
    #[Groups(['create_type'])]
    private $description = null;

    /** @OA\Property(type="integer", description="1 - Компьютер, 2 - Комната") */
    #[Assert\NotBlank(groups: ['create_computer'])]
    #[Assert\Choice(callback: [EntityType::class, 'getTypes'], groups: ['create_type'])]
    #[Groups(['create_type'])]
    private $type = null;

    /** @OA\Property(property="file1", type="file", description="(Макс. 5 файлов) jpg, jpeg, png, mp4") */
    #[Groups(['create_type'])]
    private $file1;

    /** @OA\Property(property="file2", type="file", description="(Макс. 5 файлов) jpg, jpeg, png, mp4") */
    #[Groups(['create_type'])]
    private $file2;

    /** @OA\Property(property="file3", type="file", description="(Макс. 5 файлов) jpg, jpeg, png, mp4") */
    #[Groups(['create_type'])]
    private $file3;

    /** @OA\Property(property="file4", type="file", description="(Макс. 5 файлов) jpg, jpeg, png, mp4") */
    #[Groups(['create_type'])]
    private $file4;

    /** @OA\Property(property="file5", type="file", description="(Макс. 5 файлов) jpg, jpeg, png, mp4") */
    #[Groups(['create_type'])]
    private $file5;

    /**
     * @return null
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param null $price
     * @return EntityTypeDTO
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param null $description
     * @return EntityTypeDTO
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param null $type
     * @return EntityTypeDTO
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFile1()
    {
        return $this->file1;
    }

    /**
     * @param mixed $file1
     * @return EntityTypeDTO
     */
    public function setFile1($file1)
    {
        $this->file1 = $file1;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFile2()
    {
        return $this->file2;
    }

    /**
     * @param mixed $file2
     * @return EntityTypeDTO
     */
    public function setFile2($file2)
    {
        $this->file2 = $file2;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFile3()
    {
        return $this->file3;
    }

    /**
     * @param mixed $file3
     * @return EntityTypeDTO
     */
    public function setFile3($file3)
    {
        $this->file3 = $file3;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFile4()
    {
        return $this->file4;
    }

    /**
     * @param mixed $file4
     * @return EntityTypeDTO
     */
    public function setFile4($file4)
    {
        $this->file4 = $file4;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFile5()
    {
        return $this->file5;
    }

    /**
     * @param mixed $file5
     * @return EntityTypeDTO
     */
    public function setFile5($file5)
    {
        $this->file5 = $file5;
        return $this;
    }

}