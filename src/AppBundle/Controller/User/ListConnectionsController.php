<?php
namespace AppBundle\Controller\User;

use AppBundle\Repository\UserRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\Annotations as Rest;

final class ListConnectionsController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    public function __construct(UserRepository $userRepository, ManagerRegistry $managerRegistry)
    {
        $this->userRepository = $userRepository;
        $this->managerRegistry = $managerRegistry;
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
}
