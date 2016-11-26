<?php
namespace AppBundle\Controller\User;

use AppBundle\Controller\BaseController;
use AppBundle\Repository\UserRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;

final class ListConnectionsController extends BaseController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    public function __construct(SerializerInterface $serializer, UserRepository $userRepository, ManagerRegistry $managerRegistry)
    {
        parent::__construct($serializer);
        $this->userRepository = $userRepository;
        $this->managerRegistry = $managerRegistry;
    }

    public function getUserConnectionsAction($userId)
    {
        $user = $this->managerRegistry->getManager()
            ->getRepository('AppBundle:User')
            ->findOneBy(['id' => $userId]);

        return $this->createApiResponse(['connections' => $user->getConnections()], Response::HTTP_OK);
    }
}
