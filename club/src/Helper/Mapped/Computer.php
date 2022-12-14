<?php

namespace App\Helper\Mapped;

class Computer
{
    private int $id;
    private string $description;
    private ?int $price;
    private int $status;
    /** @var String[] */
    private array $files;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Computer
     */
    public function setId(int $id): Computer
    {
        $this->id = $id;
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
     * @return Computer
     */
    public function setDescription(string $description): Computer
    {
        $this->description = $description;
        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): Computer
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return Computer
     */
    public function setStatus(int $status): Computer
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return array
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @param array $files
     * @return Computer
     */
    public function setFiles(array $files): Computer
    {
        $this->files = $files;
        return $this;
    }

}