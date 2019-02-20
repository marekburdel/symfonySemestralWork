<?php


namespace App\Controller\Rest;


use App\Entity\Group;
use App\Entity\Reservation;
use App\Entity\Room;
use App\Repository\GroupRepository;
use App\Repository\ReservationRepository;
use App\Repository\RoomRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GroupController
 * @package App\Controller\Rest
 *
 * @Rest\NamePrefix("api")
 * @Rest\RouteResource("Group")
 * @Rest\View(serializerEnableMaxDepthChecks=true)
 */
class GroupController
    extends FOSRestController
{

    /**
     * Retrieves an Group resource
     * @Rest\Get("api/group/{groupId}")
     * @param $groupId
     * @return Room|null|object
     */

    public function getAction ( $groupId )
    {
        $group = $this->getRepository()->find($groupId);
        if ( !$group ) {
            throw $this->createNotFoundException();
        }
        return View::create($group, Response::HTTP_OK);
    }

    /**
     * Retrieves Groups resource
     * @Rest\Get("api/groups")
     * @return Group[]|null|object
     */

    public function cgetAction ()
    {
        return $this->getRepository()->findAll();
    }


    /**
     * @return GroupRepository|\Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getRepository ()
    {
        return $this->getDoctrine()->getRepository(Group::class);
    }
}