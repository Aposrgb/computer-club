<?php

namespace App\Helper\Mapped;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class Room
{
    private int $id;
    /** @OA\Property(type="array", @OA\Items(ref=@Model(type="App\Helper\Mapped\Computer"))) */
    private array $computers = [];
    private string $description;
    private ?int $price;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Room
     */
    public function setId(int $id): Room
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return array
     */
    public function getComputers(): array
    {
        return $this->computers;
    }

    /**
     * @param array $computers
     * @return Room
     */
    public function setComputers(array $computers): Room
    {
        $this->computers = $computers;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Room
     */
    public function setDescription(string $description): Room
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPrice(): ?int
    {
        return $this->price;
    }

    /**
     * @param int|null $price
     * @return Room
     */
    public function setPrice(?int $price): Room
    {
        $this->price = $price;
        return $this;
    }

}