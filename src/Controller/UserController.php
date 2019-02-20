<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserUserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController
    extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/user", name="user_list")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function actionList ()
    {
        $sign = $this->getUser();
        $can = ($sign != null && in_array("ROLE_ADMIN", $sign->getRoles()));
        $users = $this->getRepository(User::class)->findAll();
        return $this->render('user/user_list.html.twig', [
            'users' => $users, 'can' => $can, 'sign' => $sign
        ]);
    }
    /**
     * @Route("/user/{id}", name="user_detail", requirements={"id": "\d+"})
     *
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailAction (User $user)
    {
        $sign = $this->getUser();
        $can = ($sign == $user || ($sign != null && in_array("ROLE_ADMIN", $sign->getRoles())));
        $canshowunconfirmedres = ($sign != null && in_array("ROLE_ADMIN", $sign->getRoles()));
        return $this->render('user/user_detail.html.twig', [
            'user' => $user, 'can'=>$can, 'canshowunconfirmedres' => $canshowunconfirmedres
        ]);
    }

    /**
     * @Route("/user/{id}/group", name="user_groups", requirements={"id": "\d+"})
     *
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userGroupsAction (User $user)
    {
        $sign = $this->getUser();
        $can = ($sign == $user || ($sign != null && in_array("ROLE_ADMIN", $sign->getRoles())));
        if($can) {
        return $this->render('user/user_groups.html.twig', [
            'user' => $user
        ]);
        }
        else{
            return $this->render('security/permission_denied.html.twig');
        }
    }

    /**
     * @Route("/user/{id}/reservation", name="user_reservations", requirements={"id": "\d+"})
     *
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userReservationsAction (User $user)
    {
        $sign = $this->getUser();
        $can = ($sign == $user || ($sign != null && in_array("ROLE_ADMIN", $sign->getRoles())));
        if($can) {
        return $this->render('user/user_reservations.html.twig', [
            'user' => $user
        ]);
        }
        else{
            return $this->render('security/permission_denied.html.twig');
        }
    }

    /**
     * @Route("/user/{id}/room", name="user_rooms", requirements={"id": "\d+"})
     *
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userRoomsAction (User $user)
    {
        $sign = $this->getUser();
        $can = ($sign == $user || ($sign != null && in_array("ROLE_ADMIN", $sign->getRoles())));
        if($can) {
        return $this->render('user/user_rooms.html.twig', [
            'user' => $user
        ]);
        }
        else{
            return $this->render('security/permission_denied.html.twig');
        }
    }

    /**
     * @Route("/user/create", name="user_create", defaults={"id": null})
     * @Route("/user/{id}/edit", name="user_edit", requirements={"id": "\d+"})
     *
     * @param int|null $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAccountAction ($id, Request $request)
    {
        $user = $id ? $this->getRepository(User::class)->find($id) : new User();
//        if ($id != null) {
//            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Cannot edit account detail, you need to be administrator');
//        }
        $sign = $this->getUser();
        if($sign == null || !in_array("ROLE_ADMIN", $sign->getRoles()))
        {
            $form = $this->createForm(UserUserType::class, $user, []);
        }
        else
        {
            $form = $this->createForm(UserType::class, $user, []);
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode password
            $password = $this->passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            // save
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User was created successfully.');
            return $this->redirectToRoute('user_detail', [
                'id' => $user->getId(),
            ]);
        }

        if ( $id ) {
            $can = ($sign == $user || ($sign != null && in_array("ROLE_ADMIN", $sign->getRoles())));
            if($can) {
            return $this->render('user/user_edit.html.twig', [
                'form' => $form->createView(),
                'user' => $user
            ]);
            }
            else{
                return $this->render('security/permission_denied.html.twig');
            }
        } else {
            return $this->render('user/user_create.html.twig', [
                'form' => $form->createView(),
            ]);
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
