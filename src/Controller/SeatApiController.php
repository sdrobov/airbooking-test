<?php


namespace App\Controller;


use App\Entity\Flight;
use App\Entity\Seat;
use App\Entity\User;
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

        $seats = $flight->getSeats()->filter(
            function (Seat $seat) {
                return $seat->getBookedAt() === null && $seat->getSelledAt() === null;
            }
        );

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
        /** @var User $user */
        $user = $this->getUser();

        /** @var Seat $ourSeat */
        $ourSeat = null;
        foreach ($flight->getSeats() as $seat) {
            if (
                $seat->getSeatNum() === (int)$seatNum
                && $seat->getSelledAt() === null
                && (
                    $seat->getBookedAt() === null
                    || $user->getBookedSeats()->contains($seat)
                )
            ) {
                $ourSeat = $seat;

                break;
            }
        }

        if (!$ourSeat) {
            throw $this->createNotFoundException("Seat {$seatNum} in flight {$flightId} not found");
        }

        if ($ourSeat->getBookedAt() === null) {
            $user->addBookedSeat($ourSeat);
            $ourSeat->setBookedAt(new DateTime());

            $em = $this->seatRepository
                ->createQueryBuilder('s')
                ->getEntityManager();
            $em->persist($ourSeat);
            $em->flush();
        }

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
        /** @var User $user */
        $user = $this->getUser();

        /** @var Seat $ourSeat */
        $ourSeat = null;
        foreach ($flight->getSeats() as $seat) {
            if (
                $seat->getSeatNum() === (int)$seatNum
                && (
                    $seat->getSelledAt() === null
                    || $user->getBoughtSeats()->contains($seat)
                )
                && (
                    $seat->getBookedAt() === null
                    || $user->getBookedSeats()->contains($seat)
                )
            ) {
                $ourSeat = $seat;

                break;
            }
        }

        if (!$ourSeat) {
            throw $this->createNotFoundException("Seat {$seatNum} in flight {$flightId} not found");
        }

        if ($ourSeat->getSelledAt() !== null) {
            $user->addBoughtSeat($ourSeat);
            $ourSeat->setSelledAt(new DateTime());

            $em = $this->seatRepository
                ->createQueryBuilder('s')
                ->getEntityManager();
            $em->persist($ourSeat);
            $em->flush();
        }

        return $this->json($ourSeat);
    }

    /**
     * @Route("/api/v1/flight/{flightId}/seat/{seatNum}/book", methods={"DELETE"})
     *
     * @param $flightId
     * @param $seatNum
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function cancelBook($flightId, $seatNum): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var Seat $ourSeat */
        $ourSeat = null;
        foreach ($user->getBookedSeats() as $seat) {
            if (
                $seat->getFlight() === (int)$flightId
                && $seat->getSeatNum() === (int)$seatNum
            ) {
                $ourSeat = $seat;

                break;
            }
        }

        if (!$ourSeat) {
            throw $this->createNotFoundException("Seat {$seatNum} in flight {$flightId} not found");
        }

        $user->removeBookedSeat($ourSeat);
        $ourSeat->setBookedAt(null);

        $em = $this->seatRepository
            ->createQueryBuilder('s')
            ->getEntityManager();
        $em->persist($ourSeat);
        $em->flush();

        return $this->json(['status' => 'ok']);
    }

    /**
     * @Route("/api/v1/flight/{flightId}/seat/{seatNum}/buy", methods={"DELETE"})
     *
     * @param $flightId
     * @param $seatNum
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function cancelBuy($flightId, $seatNum): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var Seat $ourSeat */
        $ourSeat = null;
        foreach ($user->getBoughtSeats() as $seat) {
            if (
                $seat->getFlight() === (int)$flightId
                && $seat->getSeatNum() === (int)$seatNum
            ) {
                $ourSeat = $seat;

                break;
            }
        }

        if (!$ourSeat) {
            throw $this->createNotFoundException("Seat {$seatNum} in flight {$flightId} not found");
        }

        $user->removeBoughtSeat($ourSeat);
        $ourSeat->setSelledAt(null);
        $ourSeat->setBookedAt(null);
        $ourSeat->setBookedBy(null);

        $em = $this->seatRepository
            ->createQueryBuilder('s')
            ->getEntityManager();
        $em->persist($ourSeat);
        $em->flush();

        return $this->json(['status' => 'ok']);
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
