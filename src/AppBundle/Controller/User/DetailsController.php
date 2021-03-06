<?php
namespace AppBundle\Controller\User;

use AppBundle\Controller\BaseController;
use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class DetailsController extends BaseController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(SerializerInterface $serializer, UserRepository $userRepository)
    {
        parent::__construct($serializer);
        $this->userRepository = $userRepository;
    }

    public function getAction($username)
    {
        $user = $this->userRepository->findOneBy(['username' => $username]);

        if (!$user instanceof User) {
            throw new NotFoundHttpException(sprintf(
                'No user found with username "%s"',
                $username
            ));
        }

        return $this->createApiResponse(['user' => $user], Response::HTTP_OK);
    }
}
