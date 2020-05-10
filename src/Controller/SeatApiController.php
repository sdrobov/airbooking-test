<?php


namespace App\Controller;


use App\Entity\Flight;
use App\Entity\Seat;
use App\Repository\FlightRepository;
use App\Repository\SeatRepository;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Routing\Annotation\Route;

class SeatApiController extends AbstractController
{
    /** @var SeatRepository */
    private $seatRepository;

    /** @var FlightRepository */
    private $flightRepository;

    public function __construct(SeatRepository $seatRepository, FlightRepository $flightRepository)
    {
        $this->seatRepository = $seatRepository;
        $this->flightRepository = $flightRepository;
    }

    /**
     * @Route("/api/v1/flight/{flightId}/seats", methods={"GET"})
     *
     * @param int $flightId
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function seatList($flightId): JsonResponse
    {
        $flight = $this->getFlight($flightId);

        $seats = $flight->getSeats()->filter(function (Seat $seat) {
            return $seat->getBookedAt() === null && $seat->getSelledAt() === null;
        });

        return $this->json($seats);
    }

    /**
     * @Route("/api/v1/flight/{flightId}/seat/{seatNum}/book", methods={"POST"})
     *
     * @param int $flightId
     * @param int $seatNum
     * @return JsonResponse
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function bookSeat($flightId, $seatNum): JsonResponse
    {
        $flight = $this->getFlight($flightId);

        /** @var Seat $ourSeat */
        $ourSeat = null;
        foreach ($flight->getSeats() as $seat) {
            if (
                $seat->getSeatNum() === (int)$seatNum
                && $seat->getSelledAt() === null
                && $seat->getBookedAt() === null
            ) {
                $ourSeat = $seat;

                break;
            }
        }

        if (!$ourSeat) {
            throw $this->createNotFoundException("Seat {$seatNum} in flight {$flightId} not found");
        }

        $ourSeat->setBookedAt(new DateTime());

        $em = $this->seatRepository
            ->createQueryBuilder('s')
            ->getEntityManager();
        $em->persist($ourSeat);
        $em->flush();

        return $this->json($ourSeat);
    }

    /**
     * @Route("/api/v1/flight/{flightId}/seat/{seatNum}/buy", methods={"POST"})
     *
     * @param int $flightId
     * @param int $seatNum
     * @return JsonResponse
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function buySeat($flightId, $seatNum): JsonResponse
    {
        $flight = $this->getFlight($flightId);

        /** @var Seat $ourSeat */
        $ourSeat = null;
        foreach ($flight->getSeats() as $seat) {
            if ($seat->getSeatNum() === (int)$seatNum && $seat->getSelledAt() === null) {
                if ($seat->getBookedAt() === null || $seat->getBookedBy()) {

                }
                $ourSeat = $seat;

                break;
            }
        }

        if (!$ourSeat) {
            throw $this->createNotFoundException("Seat {$seatNum} in flight {$flightId} not found");
        }

        $ourSeat->setBookedAt(new DateTime());

        $em = $this->seatRepository
            ->createQueryBuilder('s')
            ->getEntityManager();
        $em->persist($ourSeat);
        $em->flush();

        return $this->json($ourSeat);
    }

    /**
     * @param int $flightId
     * @return Flight
     * @throws NonUniqueResultException
     */
    private function getFlight($flightId): Flight
    {
        $fqb = $this->flightRepository->createQueryBuilder('f');
        /** @var Flight $flight */
        $flight = $fqb->where($fqb->expr()->eq('f.id', ':id'))
            ->andWhere($fqb->expr()->isNull('f.canceledAt'))
            ->andWhere($fqb->expr()->isNull('f.finishedAt'))
            ->setParameter('id', $flightId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$flight) {
            throw $this->createNotFoundException("Flight {$flightId} not found");
        }

        return $flight;
    }
}
