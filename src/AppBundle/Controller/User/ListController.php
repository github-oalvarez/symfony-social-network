<?php
namespace AppBundle\Controller\User;

use AppBundle\Controller\BaseController;
use AppBundle\Pagination\PaginationFactory;
use AppBundle\Repository\UserRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ListController extends BaseController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var PaginationFactory
     */
    private $paginationFactory;

    public function __construct(
        SerializerInterface $serializer,
        UserRepository $userRepository,
        PaginationFactory$paginationFactory
    ) {
        parent::__construct($serializer);
        $this->userRepository = $userRepository;
        $this->paginationFactory = $paginationFactory;
    }

    public function getAction(Request $request)
    {
        $queryBuilder = $this->userRepository->findAllQueryBuilder();

        $paginatedCollection = $this->paginationFactory->createCollection(
            $queryBuilder, $request, $request->get('_route')
        );

        return $this->createApiResponse($paginatedCollection, Response::HTTP_OK);
    }
}
