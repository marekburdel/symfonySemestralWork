<?php


namespace App\Controller\Rest;


use App\Entity\Room;
use App\Repository\RoomRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RoomController
 * @package App\Controller\Rest
 *
 * @Rest\NamePrefix("api")
 * @Rest\RouteResource("Room")
 * @Rest\View(serializerEnableMaxDepthChecks=true)
 */
class RoomController
    extends FOSRestController
{

    /**
     * Retrieves an Room resource
     * @Rest\Get("api/room/{roomId}")
     * @param $roomId
     * @return Room|null|object
     */

    public function getAction ( $roomId )
    {
        $room = $this->getRepository()->find($roomId);
        if ( !$room ) {
            throw $this->createNotFoundException();
        }
        return $room;
    }

    /**
     * Retrieves Rooms resource
     * @Rest\Get("api/rooms")
     * @return Room[]|null|object
     */

    public function cgetAction ()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * @return RoomRepository|\Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getRepository ()
    {
        return $this->getDoctrine()->getRepository(Room::class);
    }
}