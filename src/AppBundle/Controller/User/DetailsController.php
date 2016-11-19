<?php
namespace AppBundle\Controller\User;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class DetailsController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Rest\View
     */
    public function getAction($userId)
    {
        $user = $this->userRepository->findOneBy(['id' => $userId]);

        if (!$user instanceof User) {
            throw new NotFoundHttpException(sprintf(
                'No user found with id "%s"',
                $userId
            ));
        }

        return array('user' => $user);
    }
}
