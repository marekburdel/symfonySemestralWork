<?php


namespace App\Controller\Rest;


use App\Entity\Reservation;
use App\Entity\Room;
use App\Repository\ReservationRepository;
use App\Repository\RoomRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ReservationController
 * @package App\Controller\Rest
 *
 * @Rest\NamePrefix("api")
 * @Rest\RouteResource("Reservation")
 * @Rest\View(serializerEnableMaxDepthChecks=true)
 */
class ReservationController
    extends FOSRestController
{
    /**
     * Retrieves an Reservation resource
     * @Rest\Get("api/room/{roomId}/reservation/{reservationId}")
     * @param $roomId
     * @param $reservationId
     * @return Reservation|null|object
     */

    public function getAction ( $roomId ,$reservationId )
    {

        $reservation = $this->getRepository(Reservation::class)->find($reservationId);
        if ( !$reservation ) {
            throw $this->createNotFoundException();
        }
        return $reservation;
    }


    /**
     * Retrieves an Reservation resource
     * @Rest\Delete("api/room/{roomId}/reservation/{reservationId}")
     * @param $roomId
     * @param $reservationId
     *
     * @return View
     */

    public function deteleAction ( $roomId ,$reservationId )
    {

        $reservation = $this->getRepository(Reservation::class)->find($reservationId);
        $room = $reservation->getReservationRoom();

        if ( !$reservation ) {
            throw $this->createNotFoundException();
        }

        $room->deleteReservation($reservation);

        $em = $this->getDoctrine()->getManager();
        $em->persist($room);
        $em->remove($reservation);
        $em->flush();
// In case our DELETE was a success we need to return a 204 HTTP NO CONTENT response. The object is deleted.
        return View::create([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Retrieves Reservations resource
     * @Rest\Get("api/room/{roomId}/reservations")
     * @param $roomId
     * @return Reservation[]|null|object
     */

    public function cgetAction ($roomId)
    {

        return $this->getRepository(Room::class)->find($roomId)->getReservations();
    }


    /**
     *
     */
    protected function getRepository($class) {
        return $this->getDoctrine()->getRepository($class);
    }
}