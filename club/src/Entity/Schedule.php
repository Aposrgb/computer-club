<?php

namespace App\Entity;

use App\Helper\EnumStatus\ScheduleStatus;
use App\Repository\ScheduleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ScheduleRepository::class)]
class Schedule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(groups: ['get_schedule'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(groups: ['get_schedule'])]
    private ?int $hours = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(groups: ['get_schedule'])]
    private ?\DateTimeInterface $dateStart = null;

    #[ORM\ManyToOne(inversedBy: 'schedules')]
    #[Groups(groups: ['get_schedule'])]
    private ?User $owner = null;

    #[ORM\ManyToOne(inversedBy: 'schedules')]
    #[Groups(groups: ['get_schedule'])]
    private ?Computer $computer = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(groups: ['get_schedule'])]
    private ?\DateTimeInterface $createdAt;

    #[ORM\Column(type: 'integer')]
    #[Groups(groups: ['get_schedule'])]
    private ?int $status;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->status = ScheduleStatus::WAIT_PAYMENT->value;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHours(): ?int
    {
        return $this->hours;
    }

    public function setHours(int $hours): self
    {
        $this->hours = $hours;

        return $this;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeInterface $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getComputer(): ?Computer
    {
        return $this->computer;
    }

    public function setComputer(?Computer $computer): self
    {
        $this->computer = $computer;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): Schedule
    {
        $this->status = $status;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): Schedule
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getPrice(): int
    {
        $price = $this->computer->getType()->getPrice();
        $price += $this->computer->getRoom()->getType()->getPrice();
        return $price;
    }
}
