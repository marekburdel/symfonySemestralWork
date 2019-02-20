<?php


namespace App\Controller\Rest;


use App\Entity\Room;
use App\Entity\User;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use DateTime;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * Class UserController
 * @package App\Controller\Rest
 *
 * @Rest\NamePrefix("api_")
 * @Rest\RouteResource("User")
 * @Rest\View(serializerEnableMaxDepthChecks=true)
 */
class UserController
    extends FOSRestController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }


    /**
     * Creates an User resource
     * @Rest\Post("api/users")
     * @param Request $request
     * @return View
     */
    public function postAction(Request $request): View
    {
        $user = new User();
        $password = $this->passwordEncoder->encodePassword($user, $request->get('password'));
        $user->setPassword($password);
        $user->setUsername($request->get('username'));
        $user->setName($request->get('name'));
        $user->setSurname($request->get('surname'));
        $user->setEmail($request->get('email'));
        $expirationDate = new DateTime($request->get('expirationDate'));
        $user->setExpirationDate($expirationDate);
        //$user->setRoles($request->get('roles'));


        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        // In case our POST was a success we need to return a 201 HTTP CREATED response
        return View::create($user, Response::HTTP_CREATED);
    }



    /**
     * Retrieves an User resource
     * @Rest\Get("api/user/{userId}")
     * @param $userId
     * @return User|null|object
     */

    public function getAction ( $userId )
    {
        $user = $this->getRepository()->find($userId);
        if ( !$user ) {
             throw $this->createNotFoundException();

        }
        return View::create($user, Response::HTTP_OK);
    }

    /**
     * Retrieves Users resource
     * @Rest\Get("api/users")
     * @return User[]|null|object
     */

    public function cgetAction ()
    {
        return $this->getRepository()->findAll();
    }



    /**
     * @return UserRepository|\Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getRepository ()
    {
        return $this->getDoctrine()->getRepository(User::class);
    }
}