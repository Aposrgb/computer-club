<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'room', targetEntity: Computer::class)]
    private Collection $computers;

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
}
