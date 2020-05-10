<?php


namespace App\Entity;


use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Seat
 * @package App\Entity
 * @ORM\Entity()
 */
class Seat
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="bigint")
     * @var int
     */
    private $seatNum;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime|null
     */
    private $bookedAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime|null
     */
    private $selledAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime|null
     */
    private $returnedAt = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Flight", inversedBy="seats")
     * @var Flight
     */
    private $flight;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getSeatNum(): int
    {
        return $this->seatNum;
    }

    /**
     * @param int $seatNum
     */
    public function setSeatNum(int $seatNum): void
    {
        $this->seatNum = $seatNum;
    }

    /**
     * @return DateTime|null
     */
    public function getBookedAt(): ?DateTime
    {
        return $this->bookedAt;
    }

    /**
     * @param DateTime|null $bookedAt
     */
    public function setBookedAt(?DateTime $bookedAt): void
    {
        $this->bookedAt = $bookedAt;
    }

    /**
     * @return DateTime|null
     */
    public function getSelledAt(): ?DateTime
    {
        return $this->selledAt;
    }

    /**
     * @param DateTime|null $selledAt
     */
    public function setSelledAt(?DateTime $selledAt): void
    {
        $this->selledAt = $selledAt;
    }

    /**
     * @return DateTime|null
     */
    public function getReturnedAt(): ?DateTime
    {
        return $this->returnedAt;
    }

    /**
     * @param DateTime|null $returnedAt
     */
    public function setReturnedAt(?DateTime $returnedAt): void
    {
        $this->returnedAt = $returnedAt;
    }

    /**
     * @return Flight
     */
    public function getFlight(): Flight
    {
        return $this->flight;
    }

    /**
     * @param Flight $flight
     */
    public function setFlight(Flight $flight): void
    {
        $this->flight = $flight;
    }
}
