<?php
namespace AppBundle\Controller\User;

use AppBundle\Repository\UserRepository;
use FOS\RestBundle\Controller\Annotations as Rest;

final class ListController
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
    public function getAction()
    {
        return ['users' => $this->userRepository->findAll()];
    }
}
