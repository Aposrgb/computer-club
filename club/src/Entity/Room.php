<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(groups: ['get_schedule', 'get_room', 'get_computer'])]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'room', targetEntity: Computer::class)]
    #[Groups(['get_room'])]
    private Collection $computers;

    #[ORM\ManyToOne(inversedBy: 'rooms')]
    #[Groups(['get_room'])]
    private ?EntityType $type = null;

    #[ORM\Column]
    #[Groups(['get_room', 'get_computer'])]
    private ?int $maxCountComputers = null;

    public function __construct()
    {
        $this->computers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Computer>
     */
    public function getComputers(): Collection
    {
        return $this->computers;
    }

    public function addComputer(Computer $computer): self
    {
        if (!$this->computers->contains($computer)) {
            $this->computers->add($computer);
            $computer->setRoom($this);
        }

        return $this;
    }

    public function removeComputer(Computer $computer): self
    {
        if ($this->computers->removeElement($computer)) {
            // set the owning side to null (unless already changed)
            if ($computer->getRoom() === $this) {
                $computer->setRoom(null);
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

    /**
     * @return int|null
     */
    public function getMaxCountComputers(): ?int
    {
        return $this->maxCountComputers;
    }

    /**
     * @param int|null $maxCountComputers
     * @return Room
     */
    public function setMaxCountComputers(?int $maxCountComputers): Room
    {
        $this->maxCountComputers = $maxCountComputers;
        return $this;
    }
}
