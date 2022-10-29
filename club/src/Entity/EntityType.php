<?php

namespace App\Entity;

use App\Repository\EntityTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EntityTypeRepository::class)]
class EntityType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get_type', 'get_computer'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['get_room', 'get_type', 'get_computer'])]
    private ?int $price = null;

    #[ORM\Column(length: 300)]
    #[Groups(['get_room', 'get_type', 'get_computer'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true)]
    #[Groups(['get_room', 'get_type', 'get_computer'])]
    private array $files = [];

    #[ORM\OneToMany(mappedBy: 'type', targetEntity: Room::class)]
    private Collection $rooms;

    #[ORM\OneToMany(mappedBy: 'type', targetEntity: Computer::class)]
    private Collection $computers;

    #[ORM\Column]
    #[Groups(['get_type'])]
    private ?int $type = null;

    public function __construct()
    {
        $this->rooms = new ArrayCollection();
        $this->computers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function addFile($file)
    {
        $this->files[] = $file;
        return $file;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function setFiles(?array $files): self
    {
        $this->files = $files;

        return $this;
    }

    /**
     * @return Collection<int, Room>
     */
    public function getRooms(): Collection
    {
        return $this->rooms;
    }

    public function addRoom(Room $room): self
    {
        if (!$this->rooms->contains($room)) {
            $this->rooms->add($room);
            $room->setType($this);
        }

        return $this;
    }

    public function removeRoom(Room $room): self
    {
        if ($this->rooms->removeElement($room)) {
            // set the owning side to null (unless already changed)
            if ($room->getType() === $this) {
                $room->setType(null);
            }
        }

        return $this;
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
            $computer->setType($this);
        }

        return $this;
    }

    public function removeComputer(Computer $computer): self
    {
        if ($this->computers->removeElement($computer)) {
            // set the owning side to null (unless already changed)
            if ($computer->getType() === $this) {
                $computer->setType(null);
            }
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * @param int|null $type
     * @return EntityType
     */
    public function setType(?int $type): EntityType
    {
        $this->type = $type;
        return $this;
    }
}
