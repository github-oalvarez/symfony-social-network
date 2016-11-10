<?php
namespace AppBundle\Controller\Api;

use AppBundle\Entity\Relationship;
use AppBundle\Entity\User;
use AppBundle\Form\RelationshipType;
use AppBundle\Form\UserType;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UserController extends Controller
{
    /**
     * @Rest\View
     * @Rest\Get("/users")
     */
    public function getUsersAction()
    {
        return ['users' => $this->getUserRepository()->findAll()];
    }

    /**
     * @Rest\View
     * @Rest\Get("/users/{userId}")
     */
    public function getUserAction($userId)
    {
        $user = $this->getUserRepository()->findOneBy(['id' => $userId]);

        if (!$user instanceof User) {
            throw $this->createNotFoundException(sprintf(
                'No user found with id "%s"',
                $userId
            ));
        }

        return array('user' => $user);
    }

    /**
     * @Rest\View
     * @Rest\Post("/users")
     */
    public function newUserAction(Request $request)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveUser($user);

            $response = new Response();
            $response->setStatusCode(Response::HTTP_CREATED);

            $response->headers->set('Location',
                $this->generateUrl('get_user', ['userId' => $user->getId()], true)
            );

            return $response;
        }

        return View::create($form, Response::HTTP_BAD_REQUEST);

    }

    /**
     * @Rest\View
     * @Rest\Get("/users/{userId}/connections")
     */
    public function getUserConnectionsAction($userId)
    {
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findOneBy(['id' => $userId]);

        return ['connections' => $user->getConnections()];
    }

    /**
     * @Rest\View
     * @Rest\Post("/users/{userId}/connections")
     */
    public function newUserConnection(Request $request)
    {
        $relationship = new Relationship();

        $form = $this->createForm(RelationshipType::class, $relationship);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveRelationship($relationship);

            $response = new Response();
            $response->setStatusCode(Response::HTTP_CREATED);

            $response->headers->set('Location',
                $this->generateUrl('get_user_connections', ['userId' => $relationship->getUser()->getId()], true)
            );

            return $response;
        }

        return View::create($form, Response::HTTP_BAD_REQUEST);
    }

    private function getUserRepository(): UserRepository
    {
        return $this->get('user_repository');
    }

    private function getEntityManager(): EntityManager
    {
        return $this->get('doctrine.orm.entity_manager');
    }

    private function saveUser($user)
    {
        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();
    }

    private function saveRelationship($relationship)
    {
        $em = $this->getEntityManager();
        $em->persist($relationship);
        $em->flush();
    }
}
