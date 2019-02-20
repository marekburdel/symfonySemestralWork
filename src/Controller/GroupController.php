<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Room;
use App\Entity\User;
use App\Form\GroupType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GroupController
    extends AbstractController
{
    /**
     * @Route("/group", name="group_list")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function actionList ()
    {
        $groups = $this->getRepository(Group::class)->findAll();
        $sign = $this->getUser();
        $can = ($sign != null && in_array("ROLE_ADMIN", $sign->getRoles()))?true:false;
        return $this->render('group/group_list.html.twig', [
            'groups' => $groups, 'can' => $can, 'sign' => $sign
        ]);
    }

    /**
     * @Route("/group/{id}", name="group_detail", requirements={"id": "\d+"})
     *
     * @param Group $group
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailAction (Group $group)
    {
        $sign = $this->getUser();
        $can = ($sign != null &&
            (
                in_array("ROLE_ADMIN", $sign->getRoles()) ||
                $group->getAdmins()->contains($sign)
            ))?true:false;
        return $this->render('group/group_detail.html.twig', [
            'group' => $group, 'can' => $can
        ]);
    }

    /**
     * @Route("/group/{id}/member", name="group_members", requirements={"id": "\d+"})
     *
     * @param Group $group
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailGroupMembersAction (Group $group)
    {
        $sign = $this->getUser();
        $can = ($sign != null &&
            (
                in_array("ROLE_ADMIN", $sign->getRoles()) ||
                $group->getAdmins()->contains($sign)
            ))?true:false;
        if($can) {
            return $this->render('group/group_members.html.twig', [
                'group' => $group
            ]);
        }
        else{
            return $this->render('security/permission_denied.html.twig');
        }
    }

    /**
     * @Route("/group/{id}/admin", name="group_admins", requirements={"id": "\d+"})
     *
     * @param Group $group
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailGroupAdminsAction (Group $group)
    {
        $sign = $this->getUser();
        $can = ($sign != null &&
            (
                in_array("ROLE_ADMIN", $sign->getRoles()) ||
                $group->getAdmins()->contains($sign)
            ))?true:false;
        if($can) {
            return $this->render('group/group_admins.html.twig', [
                'group' => $group
            ]);
        }
        else{
            return $this->render('security/permission_denied.html.twig');
        }
    }

    /**
     * @Route("/group/{id}/room", name="group_rooms", requirements={"id": "\d+"})
     *
     * @param Group $group
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailGroupRoomsAction (Group $group)
    {
        $sign = $this->getUser();
        $can = ($sign != null &&
            (
                in_array("ROLE_ADMIN", $sign->getRoles()) ||
                $group->getAdmins()->contains($sign)
            ))?true:false;
        if($can) {
            return $this->render('group/group_rooms.html.twig', [
                'group' => $group
            ]);
        }
        else{
            return $this->render('security/permission_denied.html.twig');
        }
    }


    /**
     * @Route("/group/create", name="group_create", defaults={"id": null})
     * @Route("/group/{id}/edit", name="group_edit", requirements={"id": "\d+"})
     *
     * @param int|null $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editGroupAction ($id, Request $request)
    {

        $group = $id ? $this->getRepository(Group::class)->find($id) : new Group();
//        if ($id != null) {
//            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Cannot edit account detail, you need to be administrator');
//        }
        $sign = $this->getUser();
        $can = (
        ($sign != null && in_array("ROLE_ADMIN", $sign->getRoles())) ||
            ($group != null && $group->getAdmins()->contains($sign))
        )?true:false;
        if($can) {
            $form = $this->createForm(GroupType::class, $group, []);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // save
                if ($id == null) {
                    $user = $this->getUser();
                    $group->setAdmins(array($user));
                    $group->setMembers(array($user));
                    $user->setGroupsMember(array($group));
                    $user->setGroupsAdmin(array($group));
                }

                $em = $this->getDoctrine()->getManager();

                $em->persist($group);
                $em->flush();

                $this->addFlash('success', 'Group was created successfully.');
                return $this->redirectToRoute('group_detail', [
                    'id' => $group->getId(),
                ]);
            }

            if ($id) {
                return $this->render('group/group_edit.html.twig', [
                    'form' => $form->createView(),
                    'group' => $group
                ]);
            } else {
                return $this->render('group/group_create.html.twig', [
                    'form' => $form->createView(),
                ]);
            }
        }
        else{
            return $this->render('security/permission_denied.html.twig');
        }
    }


    /**
     * @Route("/group/{id}/member/add", name="group_members_add_list", requirements={"id": "\d+"})
     *
     * @param Group $group
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addGroupMemberListAction (Group $group)
    {
        $sign = $this->getUser();
        $can = ($sign != null &&
            (
                in_array("ROLE_ADMIN", $sign->getRoles()) ||
                $group->getAdmins()->contains($sign)
            ))?true:false;
        if($can) {
            $users = $this->getRepository(User::class)->findAll();
            foreach ($users as &$user) {
                if ( in_array($user, $group->getMembers()->toArray() ) )
                {
                    unset($users[array_search($user, $users)]);
                }
            }
            return $this->render('group/group_members_add_list.html.twig', [
                'group' => $group,
                'users' => $users
            ]);
        }
        else{
            return $this->render('security/permission_denied.html.twig');
        }
    }
    /**
     * @Route("/group/{id}/member/{memberId}/delete", name="group_member_delete", requirements={"id": "\d+", "memberId": "\d+"})
     *
     * @param Group $group
     * @param int $memberId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteGroupMemberAction (Group $group, int $memberId)
    {
        $sign = $this->getUser();
        $can = ($sign != null &&
            (
                in_array("ROLE_ADMIN", $sign->getRoles()) ||
                $group->getAdmins()->contains($sign)
            ))?true:false;
        if($can) {
            $user = $this->getRepository(User::class)->find($memberId);
            $group->deleteMember($user);
            $user->deleteGroupMember($group);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->persist($group);
            $em->flush();
            return $this->redirectToRoute('group_members', [
                'id' => $group->getId()
            ]);
        }
        else{
            return $this->render('security/permission_denied.html.twig');
        }
    }

    /**
     * @Route("/group/{id}/member/{userId}/add", name="group_member_add", requirements={"id": "\d+", "userId": "\d+"})
     *
     * @param int $id
     * @param int $userId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addGroupMemberAction (int $id, int $userId)
    {
        $user = $this->getRepository(User::class)->find($userId);
        $group = $this->getRepository(Group::class)->find($id);
        $sign = $this->getUser();
        $can = ($sign != null &&
            (
                in_array("ROLE_ADMIN", $sign->getRoles()) ||
                $group->getAdmins()->contains($sign)
            ))?true:false;
        if($can) {
            $group->addMember($user);
            $user->addGroupMember($group);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->persist($group);
            $em->flush();
            return $this->redirectToRoute('group_members', [
                'id' => $group->getId()
            ]);
        }
        else{
            return $this->render('security/permission_denied.html.twig');
        }
    }


    /**
     * @Route("/group/{id}/admin/add", name="group_admins_add_list", requirements={"id": "\d+"})
     *
     * @param Group $group
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addGroupAdminListAction (Group $group)
    {
        $sign = $this->getUser();
        $can = ($sign != null &&
            (
                in_array("ROLE_ADMIN", $sign->getRoles()) ||
                $group->getAdmins()->contains($sign)
            ))?true:false;
        if($can) {
            $users = $this->getRepository(User::class)->findAll();
            foreach ($users as &$user) {
                if ( in_array($user, $group->getAdmins()->toArray() ) )
                {
                    unset($users[array_search($user, $users)]);
                }
            }
            return $this->render('group/group_admins_add_list.html.twig', [
                'group' => $group,
                'users' => $users
            ]);
        }
        else{
            return $this->render('security/permission_denied.html.twig');
        }
    }
    /**
     * @Route("/group/{id}/admin/{memberId}/delete", name="group_admin_delete", requirements={"id": "\d+", "memberId": "\d+"})
     *
     * @param Group $group
     * @param int $memberId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteGroupAdminAction (Group $group, int $memberId)
    {
        $sign = $this->getUser();
        $can = ($sign != null &&
            (
                in_array("ROLE_ADMIN", $sign->getRoles()) ||
                $group->getAdmins()->contains($sign)
            ))?true:false;
        if($can) {
            $user = $this->getRepository(User::class)->find($memberId);
            $group->deleteAdmin($user);
            $user->deleteGroupAdmin($group);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->persist($group);
            $em->flush();
            return $this->redirectToRoute('group_admins', [
                'id' => $group->getId()
            ]);
        }
        else{
            return $this->render('security/permission_denied.html.twig');
        }

    }

    /**
     * @Route("/group/{id}/admin/{userId}/add", name="group_admin_add", requirements={"id": "\d+", "userId": "\d+"})
     *
     * @param int $id
     * @param int $userId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addGroupAdminAction (int $id, int $userId)
    {
        $user = $this->getRepository(User::class)->find($userId);
        $group = $this->getRepository(Group::class)->find($id);
        $sign = $this->getUser();
        $can = ($sign != null &&
            (
                in_array("ROLE_ADMIN", $sign->getRoles()) ||
                $group->getAdmins()->contains($sign)
            ))?true:false;
        if($can) {
            $group->addAdmin($user);
            $user->addGroupAdmin($group);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->persist($group);
            $em->flush();
            return $this->redirectToRoute('group_admins', [
                'id' => $group->getId()
            ]);
        }
        else{
            return $this->render('security/permission_denied.html.twig');
        }
    }

    /**
     * @Route("/group/{id}/room/add", name="group_rooms_add_list", requirements={"id": "\d+"})
     *
     * @param Group $group
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addGroupRoomListAction (Group $group)
    {
        $sign = $this->getUser();
        $can = ($sign != null &&
            (
                in_array("ROLE_ADMIN", $sign->getRoles()) ||
                $group->getAdmins()->contains($sign)
            ))?true:false;
        if($can) {
            $rooms = $this->getRepository(Room::class)->findAll();
            foreach ($rooms as &$room) {
                if ( in_array($room, $group->getRooms()->toArray() ) )
                {
                    unset($rooms[array_search($room, $rooms)]);
                }
            }
            return $this->render('group/group_rooms_add_list.html.twig', [
                'group' => $group,
                'rooms' => $rooms
            ]);
        }
        else{
            return $this->render('security/permission_denied.html.twig');
        }
    }
    /**
     * @Route("/group/{id}/room/{roomId}/delete", name="group_room_delete", requirements={"id": "\d+", "roomId": "\d+"})
     *
     * @param Group $group
     * @param int $roomId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteGroupRoomAction (Group $group, int $roomId)
    {
        $sign = $this->getUser();
        $can = ($sign != null &&
            (
                in_array("ROLE_ADMIN", $sign->getRoles()) ||
                $group->getAdmins()->contains($sign)
            ))?true:false;
        if($can) {
            $room = $this->getRepository(Room::class)->find($roomId);
            $group->deleteRoom($room);
            $room->setGroup(null);

            $em = $this->getDoctrine()->getManager();
            $em->persist($room);
            $em->persist($group);
            $em->flush();
            return $this->redirectToRoute('group_rooms', [
                'id' => $group->getId()
            ]);
        }
        else{
            return $this->render('security/permission_denied.html.twig');
        }

    }

    /**
     * @Route("/group/{id}/room/{roomId}/add", name="group_room_add", requirements={"id": "\d+", "roomId": "\d+"})
     *
     * @param int $id
     * @param int $roomId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addGroupRoomAction (int $id, int $roomId)
    {
        $room = $this->getRepository(Room::class)->find($roomId);
        $group = $this->getRepository(Group::class)->find($id);

        $sign = $this->getUser();
        $can = ($sign != null &&
            (
                in_array("ROLE_ADMIN", $sign->getRoles()) ||
                $group->getAdmins()->contains($sign)
            ))?true:false;
        if($can) {
            $group->addRoom($room);
            $room->setGroup($group);

            $em = $this->getDoctrine()->getManager();
            $em->persist($room);
            $em->persist($group);
            $em->flush();
            return $this->redirectToRoute('group_rooms', [
                'id' => $group->getId()
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
