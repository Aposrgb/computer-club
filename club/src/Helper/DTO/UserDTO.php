<?php

namespace App\Helper\DTO;

use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class UserDTO
{
    /** @OA\Property(type="string") */
    #[Assert\NotBlank(groups: ['registration'])]
    #[Assert\Email(groups: ['registration'])]
    #[Assert\Length(min: 6, max: 30, groups: ['registration'])]
    #[Assert\Type(type: 'string', groups: ['registration'])]
    #[Groups(groups: ['registration'])]
    private $email;

    /** @OA\Property(type="string") */
    #[Assert\NotBlank(groups: ['registration'])]
    #[Assert\Length(min: 6, max: 30, groups: ['registration'])]
    #[Assert\Type(type: 'string', groups: ['registration'])]
    #[Groups(groups: ['registration'])]
    private $password;

    /** @OA\Property(type="string") */
    #[Assert\NotBlank(groups: ['registration'])]
    #[Assert\Length(min: 2, max: 20, groups: ['registration'])]
    #[Assert\Type(type: 'string', groups: ['registration'])]
    #[Groups(groups: ['registration'])]
    private $name = null;

    /** @OA\Property(type="string") */
    #[Assert\NotBlank(groups: ['registration'])]
    #[Assert\Length(min: 2, max: 20, groups: ['registration'])]
    #[Assert\Type(type: 'string', groups: ['registration'])]
    #[Groups(groups: ['registration'])]
    private $surname = null;

    /** @OA\Property(type="string") */
    #[Assert\Type(type: 'string', groups: ['registration'])]
    #[Groups(groups: ['registration'])]
    private $phone;

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return UserDTO
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     * @return UserDTO
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param null $name
     * @return UserDTO
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return null
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param null $surname
     * @return UserDTO
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     * @return UserDTO
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

}