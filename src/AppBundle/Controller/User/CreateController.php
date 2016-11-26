<?php
namespace AppBundle\Controller\User;

use AppBundle\Controller\BaseController;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Repository\UserRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
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
        FormFactoryInterface $formFactory,
        ManagerRegistry $managerRegistry,
        UrlGeneratorInterface $urlGenerator
    ) {
        parent::__construct($serializer);
        $this->userRepository = $userRepository;
        $this->formFactory = $formFactory;
        $this->managerRegistry = $managerRegistry;
        $this->urlGenerator = $urlGenerator;
    }

    public function postAction(Request $request)
    {
        $user = new User();
        $form = $this->formFactory->create(UserType::class, $user);
        $this->processForm($request, $form);

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

    private function processForm(Request $request, FormInterface $form)
    {
        $data = json_decode($request->getContent(), true);
        $clearMissing = $request->getMethod() != 'PATCH';
        $form->submit($data, $clearMissing);
    }
}
