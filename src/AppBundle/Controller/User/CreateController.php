<?php
namespace AppBundle\Controller\User;

use AppBundle\Controller\BaseController;
use AppBundle\Form\UserType;
use AppBundle\Repository\UserRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\UserBundle\Doctrine\UserManager;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class CreateController extends BaseController
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
        SerializerInterface $serializer,
        UserRepository $userRepository,
        UserManager $userManager,
        FormFactoryInterface $formFactory,
        ManagerRegistry $managerRegistry,
        UrlGeneratorInterface $urlGenerator
    ) {
        parent::__construct($serializer);
        $this->userRepository = $userRepository;
        $this->userManager = $userManager;
        $this->formFactory = $formFactory;
        $this->managerRegistry = $managerRegistry;
        $this->urlGenerator = $urlGenerator;
    }

    public function postAction(Request $request)
    {
        $user = $this->userManager->createUser();

        $form = $this->formFactory->create(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->managerRegistry->getManager()->persist($user);
            $this->managerRegistry->getManager()->flush();

            $response = $this->createApiResponse($user, Response::HTTP_CREATED);

            $userUrl = $this->urlGenerator->generate(
                'user_list',
                ['userId' => $user->getId()]
            );
            $response->headers->set('Location', $userUrl);

            return $response;
        }

        return $this->createApiResponse($form, Response::HTTP_BAD_REQUEST);
    }
}
