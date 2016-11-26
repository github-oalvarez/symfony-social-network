<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Friendship;
use AppBundle\Entity\Relationship;
use AppBundle\Entity\User;
use AppBundle\Form\FriendshipType;
use AppBundle\Repository\UserRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * UserController constructor.
     * @param UserRepository $userRepository
     * @param UserManager $userManager
     * @param FormFactoryInterface $formFactory
     * @param ManagerRegistry $managerRegistry
     * @param RouterInterface $router
     */
    public function __construct(
        UserRepository $userRepository,
        UserManager $userManager,
        FormFactoryInterface $formFactory,
        ManagerRegistry $managerRegistry,
        RouterInterface $router
    ) {
        $this->userRepository = $userRepository;
        $this->userManager = $userManager;
        $this->formFactory = $formFactory;
        $this->managerRegistry = $managerRegistry;
        $this->router = $router;
    }

    /**
     * @Rest\View
     * @Rest\Get("/users/{userId}/connections")
     */
    public function getUserConnectionsAction($userId)
    {
        $user = $this->managerRegistry->getManager()
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
        $friendship = new Friendship();

        $form = $this->formFactory->create(FriendshipType::class, $friendship);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveFriendship($friendship);

            $response = new Response();
            $response->setStatusCode(Response::HTTP_CREATED);

            $response->headers->set('Location',
                $this->router->generate('get_user_connections', ['userId' => $friendship->getUser()->getId()], true)
            );

            return $response;
        }

        return View::create($form, Response::HTTP_BAD_REQUEST);
    }

    private function saveFriendship($friendship)
    {
        $this->managerRegistry->getManager()->persist($friendship);
        $this->managerRegistry->getManager()->flush();
    }
}
