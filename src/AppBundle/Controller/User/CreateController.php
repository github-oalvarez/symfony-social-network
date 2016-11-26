<?php
namespace AppBundle\Controller\User;

use AppBundle\Form\UserType;
use AppBundle\Repository\UserRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class CreateController
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
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(
        UserRepository $userRepository,
        UserManager $userManager,
        FormFactoryInterface $formFactory,
        ManagerRegistry $managerRegistry,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->userRepository = $userRepository;
        $this->userManager = $userManager;
        $this->formFactory = $formFactory;
        $this->managerRegistry = $managerRegistry;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Rest\View
     */
    public function postAction(Request $request)
    {
        $user = $this->userManager->createUser();

        $form = $this->formFactory->create(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->managerRegistry->getManager()->persist($user);
            $this->managerRegistry->getManager()->flush();

            $response = new Response();
            $response->setStatusCode(Response::HTTP_CREATED);

            $response->headers->set('Location',
                $this->urlGenerator->generate('user_list', ['userId' => $user->getId()], true)
            );

            return $response;
        }

        return View::create($form, Response::HTTP_BAD_REQUEST);
    }
}
