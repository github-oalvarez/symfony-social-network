<?php
namespace AppBundle\Controller\User;

use AppBundle\Controller\BaseController;
use AppBundle\Pagination\PaginatedCollection;
use AppBundle\Repository\UserRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\SerializerInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ListController extends BaseController
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

    public function getAction(Request $request)
    {
        $page = $request->query->get('page', 1);

        $qb = $this->userRepository
            ->findAllQueryBuilder();

        $adapter = new DoctrineORMAdapter($qb);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(10);
        $pagerfanta->setCurrentPage($page);

        $users = [];
        foreach ($pagerfanta->getCurrentPageResults() as $result) {
            $users[] = $result;
        }

        $paginatedCollection = new PaginatedCollection($users, $pagerfanta->getNbResults());

        return $this->createApiResponse($paginatedCollection, Response::HTTP_OK);
    }
}
