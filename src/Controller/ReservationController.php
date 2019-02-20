<?php

namespace App\Controller;


use App\Entity\Reservation;
use App\Entity\Room;
use App\Form\ReservationCreateUserType;
use App\Form\ReservationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController
    extends AbstractController
{


    /**
     * @Route("/room/{id}/reservation", name="reservation_room_list", requirements={"id": "\d+"})
     *
     * @param Room $room
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction (Room $room)
    {
        $can = ($this->getUser()!=null)?true:false;
        if($can) {
            //$reservations = get all reservation for room
            $reservations = $this->getRepository(Reservation::class)->findBy(array('reservation_room' => $room->getId()));

            return $this->render('reservation/reservation_room_list.html.twig', [
                'room' => $room,
                'reservations' => $reservations
            ]);
        }
        else{
            return $this->render('security/permission_denied.html.twig');
        }
    }

    /**
     * @Route("/reservation/unconfirmed", name="unconfirmed_reservations")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listUnconfirmedAction ()
    {
        $sign = $this->getUser();
        $can = ($sign != null && in_array("ROLE_ADMIN", $sign->getRoles()));
        if($can) {
            $reservations = $this->getRepository(Reservation::class)->findAll();

            return $this->render('reservation/reservation_unconfirmed_list.html.twig', [
                'reservations' => $reservations
            ]);
        }
        else{
            return $this->render('security/permission_denied.html.twig');
        }
    }

    /**
     * @Route("/room/{roomId}/reservation/create", name="reservation_create", defaults={"id": null})
     * @Route("/room/{roomId}/reservation/{id}/edit", name="reservation_edit", requirements={"id": "\d+", "roomId"="\d+"})
     *
     * @param int $roomId
     * @param int|null $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editReservationRoom (int $roomId, $id, Request $request)
    {
        $reservation = $id ? $this->getRepository(Reservation::class)->find($id) : new Reservation();
//        if ($id != null) {
//            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Cannot edit account detail, you need to be administrator');
//        }
        $sign = $this->getUser();
        if($sign!=null) {
            $room = $this->getRepository(Room::class)->find($roomId);
            $user = $this->getUser();

            if(!in_array("ROLE_ADMIN", $sign->getRoles()))
            {
                $form = $this->createForm(ReservationCreateUserType::class, $reservation, []);
            }
            else {
                $form = $this->createForm(ReservationType::class, $reservation, []);
            }
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if ($id == null) {
                    $reservation->setUserInitiator($user);
                    $reservation->setReservationRoom($room);
                    $reservation->setReservationAdmin(null);
                    $room->addReservation($reservation);
                }
                // save
                $em = $this->getDoctrine()->getManager();
                $em->persist($room);
                $em->persist($reservation);
                $em->flush();

                $this->addFlash('success', 'Room was created successfully.');
                return $this->redirectToRoute('reservation_room_list', [
                    'id' => $roomId,
                ]);
            }
            if ($id) {
                $can2 = in_array("ROLE_ADMIN", $sign->getRoles())?true:false;
                if($can2){
                    return $this->render('reservation/reservation_room_edit.html.twig', [
                        'form' => $form->createView(),
                        'reservation' => $reservation,
                        'room' => $room
                    ]);
                }
                else{
                    return $this->render('security/permission_denied.html.twig');
                }
            } else {
                return $this->render('reservation/reservation_room_create.html.twig', [
                    'form' => $form->createView(),
                    'room' => $room
                ]);
            }
        }
        else{
            return $this->render('security/permission_denied.html.twig');
        }
    }



    /**
     * @Route("/room/{roomId}/reservation/{id}/delete", name="reservation_delete", requirements={"id": "\d+", "roomId"="\d+"})
     *
     * @param int $roomId
     * @param int $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteReservationRoom (int $roomId, $id, Request $request)
    {
        $sign = $this->getUser();
        $reservation = $this->getRepository(Reservation::class)->find($id);

        $can = ($sign != null && (in_array("ROLE_ADMIN", $sign->getRoles()) ||
        $sign == $reservation->getUserInitiator()));
        if($can) {
        $room = $reservation->getReservationRoom();
        $room->deleteReservation($reservation);


        $em = $this->getDoctrine()->getManager();
        $em->persist($room);
        $em->remove($reservation);
        $em->flush();

        return $this->redirectToRoute('reservation_room_list', [
            'id' => $roomId
        ]);
        }
        else{
            return $this->render('security/permission_denied.html.twig');
        }
    }





    /**
     * @param $class
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getRepository($class) {
        return $this->getDoctrine()->getRepository($class);
    }
}
