<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\User;
use App\Form\RoomType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RoomController
    extends AbstractController
{
    /**
     * @Route("/room", name="room_list")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function actionList ()
    {
        $rooms = $this->getRepository(Room::class)->findAll();
        $sign = $this->getUser();
        $can = ($sign != null && in_array("ROLE_ADMIN", $sign->getRoles()))?true:false;
        $canshowres = ($sign!=null)?true:false;
        return $this->render('room/room_list.html.twig', [
            'rooms' => $rooms, 'can' => $can, 'canshowres' => $canshowres, 'sign' => $sign
        ]);
    }

    /**
     * @Route("/room/{id}", name="room_detail", requirements={"id": "\d+"})
     *
     * @param Room $room
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailAction (Room $room)
    {
        $sign = $this->getUser();
        $can = ($sign != null &&
            (
                in_array("ROLE_ADMIN", $sign->getRoles()) ||
                $room->getAdmins()->contains($sign)
            ))?true:false;
        return $this->render('room/room_detail.html.twig', [
            'room' => $room, 'can'=>$can, 'canshowres' => ($sign != null)
        ]);
    }

    /**
     * @Route("/room/{id}/user", name="room_users", requirements={"id": "\d+"})
     *
     * @param Room $room
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailRoomUsersAction (Room $room)
    {
        $sign = $this->getUser();
        $can = ($sign != null &&
            (
                in_array("ROLE_ADMIN", $sign->getRoles()) ||
                $room->getAdmins()->contains($sign)
            ))?true:false;
        if($can) {
            return $this->render('room/room_users.html.twig', [
                'room' => $room
            ]);
        }
        else{
            return $this->render('security/permission_denied.html.twig');
        }
    }

    /**
     * @Route("/room/{id}/user/add", name="room_users_add_list", requirements={"id": "\d+"})
     *
     * @param Room $room
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addRoomUserListAction (Room $room)
    {
        $sign = $this->getUser();
        $can = ($sign != null &&
            (
                in_array("ROLE_ADMIN", $sign->getRoles()) ||
                $room->getAdmins()->contains($sign)
            ))?true:false;
        if($can) {
            $users = $this->getRepository(User::class)->findAll();
            foreach ($users as &$user) {
                if ( in_array($user, $room->getUsers()->toArray() ) )
                {
                    unset($users[array_search($user, $users)]);
                }
            }
            return $this->render('room/room_users_add_list.html.twig', [
                'room' => $room,
                'users' => $users
            ]);
        }
        else{
            return $this->render('security/permission_denied.html.twig');
        }
    }

    /**
     * @Route("/room/{id}/user/{userId}/add", name="room_user_add", requirements={"id": "\d+", "userId": "\d+"})
     *
     * @param int $id
     * @param int $userId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addRoomUserAction (int $id, int $userId)
    {
        $user = $this->getRepository(User::class)->find($userId);
        $room = $this->getRepository(Room::class)->find($id);
        $sign = $this->getUser();
        $can = ($sign != null &&
            (
                in_array("ROLE_ADMIN", $sign->getRoles()) ||
                $room->getAdmins()->contains($sign)
            ))?true:false;
        if($can) {
            $room->addUser($user);
            $user->addRoomUser($room);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->persist($room);
            $em->flush();
            return $this->redirectToRoute('room_detail', [
                'id' => $room->getId()
            ]);
        }
        else{
            return $this->render('security/permission_denied.html.twig');
        }
    }

    /**
     * @Route("/room/{id}/user/{memberId}/delete", name="room_user_delete", requirements={"id": "\d+", "memberId": "\d+"})
     *
     * @param Room $room
     * @param int $memberId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteRoomUserAction (Room $room, int $memberId)
    {
        $sign = $this->getUser();
        $can = ($sign != null &&
            (
                in_array("ROLE_ADMIN", $sign->getRoles()) ||
                $room->getAdmins()->contains($sign)
            ))?true:false;
        if($can) {
            $user = $this->getRepository(User::class)->find($memberId);
            $room->deleteUser($user);
            $user->deleteRoomUser($room);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->persist($room);
            $em->flush();
            return $this->redirectToRoute('room_detail', [
                'id' => $room->getId()
            ]);
        }
        else{
            return $this->render('security/permission_denied.html.twig');
        }

    }

    /**
     * @Route("/room/{id}/admin", name="room_admins", requirements={"id": "\d+"})
     *
     * @param Room $room
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailRoomAdminsAction (Room $room)
    {
        $sign = $this->getUser();
        $can = ($sign != null &&
            (
                in_array("ROLE_ADMIN", $sign->getRoles()) ||
                $room->getAdmins()->contains($sign)
            ))?true:false;
        if($can) {
            return $this->render('room/room_admins.html.twig', [
                'room' => $room
            ]);
        }
        else{
            return $this->render('security/permission_denied.html.twig');
        }
    }

    /**
     * @Route("/room/{id}/admin/add", name="room_admins_add_list", requirements={"id": "\d+"})
     *
     * @param Room $room
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addRoomAdminListAction (Room $room)
    {
        $sign = $this->getUser();
        $can = ($sign != null &&
            (
                in_array("ROLE_ADMIN", $sign->getRoles()) ||
                $room->getAdmins()->contains($sign)
            ))?true:false;
        if($can) {
            $users = $this->getRepository(User::class)->findAll();
            foreach ($users as &$user) {
                if ( in_array($user, $room->getAdmins()->toArray() ) )
                {
                    unset($users[array_search($user, $users)]);
                }
            }
            return $this->render('room/room_admins_add_list.html.twig', [
                'room' => $room,
                'users' => $users
            ]);
        }
        else{
            return $this->render('security/permission_denied.html.twig');
        }
    }

    /**
     * @Route("/room/{id}/admin/{userId}/add", name="room_admin_add", requirements={"id": "\d+", "userId": "\d+"})
     *
     * @param int $id
     * @param int $userId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addRoomAdminAction (int $id, int $userId)
    {
        $user = $this->getRepository(User::class)->find($userId);
        $room = $this->getRepository(Room::class)->find($id);
        $sign = $this->getUser();
        $can = ($sign != null &&
            (
                in_array("ROLE_ADMIN", $sign->getRoles()) ||
                $room->getAdmins()->contains($sign)
            ))?true:false;
        if($can) {
            $room->addAdmin($user);
            $user->addRoomAdmin($room);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->persist($room);
            $em->flush();
            return $this->redirectToRoute('room_detail', [
                'id' => $room->getId()
            ]);
        }
        else{
            return $this->render('security/permission_denied.html.twig');
        }
    }

    /**
     * @Route("/room/{id}/admin/{memberId}/delete", name="room_admin_delete", requirements={"id": "\d+", "memberId": "\d+"})
     *
     * @param Room $room
     * @param int $memberId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteRoomAdminAction (Room $room, int $memberId)
    {
        $sign = $this->getUser();
        $can = ($sign != null &&
            (
                in_array("ROLE_ADMIN", $sign->getRoles()) ||
                $room->getAdmins()->contains($sign)
            ))?true:false;
        if($can) {
            $user = $this->getRepository(User::class)->find($memberId);
            $room->deleteAdmin($user);
            $user->deleteRoomAdmin($room);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->persist($room);
            $em->flush();
            return $this->redirectToRoute('room_detail', [
                'id' => $room->getId()
            ]);
        }
        else{
            return $this->render('security/permission_denied.html.twig');
        }

    }

    /**
     * @Route("/room/create", name="room_create", defaults={"id": null})
     * @Route("/room/{id}/edit", name="room_edit", requirements={"id": "\d+"})
     *
     * @param int|null $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAccountAction ($id, Request $request)
    {
        $room = $id ? $this->getRepository(Room::class)->find($id) : new Room();
//        if ($id != null) {
//            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Cannot edit account detail, you need to be administrator');
//        }
        $sign = $this->getUser();
        $can = (
            ($sign != null && in_array("ROLE_ADMIN", $sign->getRoles())) ||
            ($room != null && $room->getAdmins()->contains($sign))
        )?true:false;
        if($can) {
            $form = $this->createForm(RoomType::class, $room, []);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // save
                $em = $this->getDoctrine()->getManager();
                $em->persist($room);
                $em->flush();

                $this->addFlash('success', 'Room was created successfully.');
                return $this->redirectToRoute('room_list', [
                    'id' => $room->getId(),
                ]);
            }

            if ($id) {
                return $this->render('room/room_edit.html.twig', [
                    'form' => $form->createView(),
                    'room' => $room
                ]);
            } else {
                return $this->render('room/room_create.html.twig', [
                    'form' => $form->createView(),
                ]);
            }
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
