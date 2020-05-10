<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements JWTUserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     * @var string
     */
    private $username;

    /**
     * @ORM\Column(type="string", unique=true)
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Seat", mappedBy="bookedBy")
     * @var Seat[]|ArrayCollection
     */
    private $bookedSeats;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Seat", mappedBy="selledTo")
     * @var Seat[]|ArrayCollection
     */
    private $boughtSeats;

    public function __construct()
    {
        $this->bookedSeats = new ArrayCollection();
        $this->boughtSeats = new ArrayCollection();
    }

    public function getId(): ?string
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
     * @return Collection|Seat[]
     */
    public function getBookedSeats(): Collection
    {
        return $this->bookedSeats;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public static function createFromPayload($username, array $payload)
    {
        $user = new static();
        $user->setEmail($payload['email']);
        $user->setUsername($username);

        return $user;
    }

    public function getRoles()
    {
        return [];
    }

    public function getSalt()
    {
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function eraseCredentials()
    {
    }

    public function addBookedSeat(Seat $bookedSeat): self
    {
        if (!$this->bookedSeats->contains($bookedSeat)) {
            $this->bookedSeats[] = $bookedSeat;
            $bookedSeat->setBookedBy($this);
        }

        return $this;
    }

    public function removeBookedSeat(Seat $bookedSeat): self
    {
        if ($this->bookedSeats->contains($bookedSeat)) {
            $this->bookedSeats->removeElement($bookedSeat);
            // set the owning side to null (unless already changed)
            if ($bookedSeat->getBookedBy() === $this) {
                $bookedSeat->setBookedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Seat[]
     */
    public function getBoughtSeats(): Collection
    {
        return $this->boughtSeats;
    }

    public function addBoughtSeat(Seat $boughtSeat): self
    {
        if (!$this->boughtSeats->contains($boughtSeat)) {
            $this->boughtSeats[] = $boughtSeat;
            $boughtSeat->setSelledTo($this);
        }

        return $this;
    }

    public function removeBoughtSeat(Seat $boughtSeat): self
    {
        if ($this->boughtSeats->contains($boughtSeat)) {
            $this->boughtSeats->removeElement($boughtSeat);
            // set the owning side to null (unless already changed)
            if ($boughtSeat->getSelledTo() === $this) {
                $boughtSeat->setSelledTo(null);
            }
        }

        return $this;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }
}
