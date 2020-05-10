<?php


namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Flight
 * @package App\Entity
 * @ORM\Entity()
 */
class Flight
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime|null
     */
    private $finishedAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime|null
     */
    private $canceledAt = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Seat", mappedBy="flight")
     * @var Seat[]|ArrayCollection
     */
    private $seats;

    public function __construct()
    {
        $this->seats = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return DateTime|null
     */
    public function getFinishedAt(): ?DateTime
    {
        return $this->finishedAt;
    }

    /**
     * @param DateTime|null $finishedAt
     */
    public function setFinishedAt(?DateTime $finishedAt): void
    {
        $this->finishedAt = $finishedAt;
    }

    /**
     * @return DateTime|null
     */
    public function getCanceledAt(): ?DateTime
    {
        return $this->canceledAt;
    }

    /**
     * @param DateTime|null $canceledAt
     */
    public function setCanceledAt(?DateTime $canceledAt): void
    {
        $this->canceledAt = $canceledAt;
    }

    /**
     * @return Seat[]|ArrayCollection
     */
    public function getSeats()
    {
        return $this->seats;
    }

    /**
     * @param Seat[]|ArrayCollection $seats
     */
    public function setSeats($seats): void
    {
        $this->seats = $seats;
    }
}
