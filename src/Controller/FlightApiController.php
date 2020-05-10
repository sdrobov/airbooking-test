<?php


namespace App\Controller;


use App\Entity\Flight;
use App\Repository\FlightRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class FlightApiController extends AbstractController
{
    public const DEFAULT_LIMIT = 100;
    public const DEFAULT_OFFSET = 0;

    /** @var FlightRepository */
    private $repository;

    public function __construct(FlightRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/api/v1/flights", methods={"GET"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function flightList(Request $request)
    {
        $limit = $request->query->get('limit', self::DEFAULT_LIMIT);
        $offset = $request->query->get('offset', self::DEFAULT_OFFSET);

        $qb = $this->repository->createQueryBuilder('f');

        /** @var Flight[]|ArrayCollection $flights */
        $flights = $qb->where($qb->expr()->isNull('f.finishedAt'))
            ->andWhere($qb->expr()->isNull('f.canceledAt'))
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();

        return $this->json($flights);
    }
}
