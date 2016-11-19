<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Relationship;
use AppBundle\Entity\User;
use AppBundle\Form\RelationshipType;
use AppBundle\Form\UserType;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

final class UserController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        UserRepository $userRepository,
        UserManager $userManager,
        FormFactoryInterface $formFactory,
        EntityManager $entityManager,
        RouterInterface $router
    ) {
        $this->userRepository = $userRepository;
        $this->userManager = $userManager;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    /**
     * @Rest\View
     */
    public function newUserAction(Request $request)
    {
        $user = $this->userManager->createUser();

        $form = $this->formFactory->create(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveUser($user);

            $response = new Response();
            $response->setStatusCode(Response::HTTP_CREATED);

            $response->headers->set('Location',
                $this->router->generate('get_user', ['userId' => $user->getId()], true)
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
        $user = $this->entityManager
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

        $form = $this->formFactory->create(RelationshipType::class, $relationship);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveRelationship($relationship);

            $response = new Response();
            $response->setStatusCode(Response::HTTP_CREATED);

            $response->headers->set('Location',
                $this->router->generate('get_user_connections', ['userId' => $relationship->getUser()->getId()], true)
            );

            return $response;
        }

        return View::create($form, Response::HTTP_BAD_REQUEST);
    }

    private function saveUser($user)
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    private function saveRelationship($relationship)
    {
        $this->entityManager->persist($relationship);
        $this->entityManager->flush();
    }
}
