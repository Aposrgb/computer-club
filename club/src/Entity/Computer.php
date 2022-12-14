<?php

namespace App\Entity;

use App\Helper\EnumStatus\ComputerStatus;
use App\Repository\ComputerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ComputerRepository::class)]
class Computer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(groups: ['get_schedule', 'get_room', 'get_computer'])]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $status = null;

    #[ORM\ManyToOne(inversedBy: 'computers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(groups: ['get_schedule', 'get_computer'])]
    private ?Room $room = null;

    #[ORM\OneToMany(mappedBy: 'computer', targetEntity: Schedule::class)]
    private Collection $schedules;

    #[ORM\ManyToOne(inversedBy: 'computers')]
    #[Groups(['get_room', 'get_computer'])]
    private ?EntityType $type = null;

    public function __construct()
    {
        $this->schedules = new ArrayCollection();
        $this->status = ComputerStatus::ACTIVE->value;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): self
    {
        $this->room = $room;

        return $this;
    }

    /**
     * @return Collection<int, Schedule>
     */
    public function getSchedules(): Collection
    {
        return $this->schedules;
    }

    public function addSchedule(Schedule $schedule): self
    {
        if (!$this->schedules->contains($schedule)) {
            $this->schedules->add($schedule);
            $schedule->setComputer($this);
        }

        return $this;
    }

    public function removeSchedule(Schedule $schedule): self
    {
        if ($this->schedules->removeElement($schedule)) {
            // set the owning side to null (unless already changed)
            if ($schedule->getComputer() === $this) {
                $schedule->setComputer(null);
            }
        }

        return $this;
    }

    public function getType(): ?EntityType
    {
        return $this->type;
    }

    public function setType(?EntityType $type): self
    {
        $this->type = $type;

        return $this;
    }

    #[Groups(['get_room'])]
    public function getPrice(): ?int
    {
        return $this->type?->getPrice() + $this?->getRoom()?->getType()?->getPrice()??0;
    }
}
