<?php

namespace App\Entity;

use App\Helper\EnumStatus\UserStatus;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(groups: ['get_schedule','get_user'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(groups: ['get_schedule','get_user'])]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /** The hashed password */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private ?int $status = null;

    #[ORM\Column(length: 255)]
    #[Groups(groups: ['get_schedule','get_user'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(groups: ['get_schedule','get_user'])]
    private ?string $surname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Device::class, cascade: ['persist', 'remove'])]
    private Collection $devices;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Schedule::class)]
    private Collection $schedules;

    public function __construct()
    {
        $this->status = UserStatus::ACTIVE->value;
        $this->devices = new ArrayCollection();
        $this->schedules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int|null $status
     * @return User
     */
    public function setStatus(?int $status): User
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return User
     */
    public function setName(?string $name): User
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSurname(): ?string
    {
        return $this->surname;
    }

    /**
     * @param string|null $surname
     * @return User
     */
    public function setSurname(?string $surname): User
    {
        $this->surname = $surname;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     * @return User
     */
    public function setPhone(?string $phone): User
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getDevices(): Collection
    {
        return $this->devices;
    }

    /**
     * @param Collection $devices
     * @return User
     */
    public function setDevices(Collection $devices): User
    {
        $this->devices = $devices;
        return $this;
    }

    public function addDevice(Device $device): self
    {
        if (!$this->devices->contains($device)) {
            $this->devices[] = $device;
            $device->setOwner($this);
        }

        return $this;
    }

    public function removeDevice(Device $device): self
    {
        if ($this->devices->removeElement($device)) {
            // set the owning side to null (unless already changed)
            if ($device->getOwner() === $this) {
                $device->setOwner(null);
            }
        }

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
            $schedule->setOwner($this);
        }

        return $this;
    }

    public function removeSchedule(Schedule $schedule): self
    {
        if ($this->schedules->removeElement($schedule)) {
            // set the owning side to null (unless already changed)
            if ($schedule->getOwner() === $this) {
                $schedule->setOwner(null);
            }
        }

        return $this;
    }
}
