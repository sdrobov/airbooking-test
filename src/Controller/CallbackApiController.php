<?php


namespace App\Controller;


use App\Repository\FlightRepository;
use DateTime;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CallbackApiController extends AbstractController
{
    /**
     * @Route("/api/v1/callback/events", methods={"POST"})
     *
     * @param Request $request
     * @param FlightRepository $flightRepository
     * @param EntityManager $em
     * @return JsonResponse
     */
    public function event(
        Request $request,
        FlightRepository $flightRepository,
        EntityManager $em
    ) {
        $payload = $request->request->get('data');
        $secretKey = $payload['secret_key'];

        if (!$this->isSecretKeyValid($secretKey)) {
            throw $this->createAccessDeniedException();
        }

        $flightId = (int)$payload['flight_id'];
        $event = (string)$payload['event'];

        $flight = $flightRepository->find($flightId);
        if (!$flight) {
            throw $this->createNotFoundException("Flight {$flightId} not found");
        }

        switch ($event) {
            case 'flight_ticket_sales_completed':
                $flight->setFinishedAt(new DateTime());

                break;

            case 'flight_canceled':
                $flight->setCanceledAt(new DateTime());

                break;

            default:
                throw new BadRequestHttpException("Unknown event {$event}");
        }

        $em->persist($flight);
        $em->flush();

        return $this->json(['status' => 'ok']);
    }

    private function isSecretKeyValid(string $secretKey): bool
    {
        return true;
    }
}
