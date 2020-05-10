<?php


namespace App\Controller;


use App\Message\SendEmailMessage;
use App\Repository\FlightRepository;
use DateTime;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class CallbackApiController extends AbstractController
{
    public const EV_FLIGHT_SOLD_OUT = 'flight_ticket_sales_completed';
    public const EV_FLIGHT_CANCELED = 'flight_canceled';

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
        EntityManager $em,
        MessageBusInterface $messageBus
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
            case self::EV_FLIGHT_SOLD_OUT:
                $flight->setFinishedAt(new DateTime());

                break;

            case self::EV_FLIGHT_CANCELED:
                $flight->setCanceledAt(new DateTime());

                break;

            default:
                throw new BadRequestHttpException("Unknown event {$event}");
        }

        $em->persist($flight);
        $em->flush();

        if ($event === self::EV_FLIGHT_CANCELED) {
            foreach ($flight->getSeats() as $seat) {
                if ($seat->getSelledTo() || $seat->getBookedBy()) {
                    $user = $seat->getSelledTo() ?? $seat->getBookedBy();
                    $messageBus->dispatch(
                        new SendEmailMessage(
                            $user,
                            'Sorry, your flight was canceled :(',
                            'Your flight canceled'
                        )
                    );
                }
            }
        }

        return $this->json(['status' => 'ok']);
    }

    private function isSecretKeyValid(string $secretKey): bool
    {
        return true;
    }
}
