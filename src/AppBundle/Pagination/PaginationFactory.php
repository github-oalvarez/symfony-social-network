<?php
namespace AppBundle\Pagination;

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class PaginationFactory
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function createCollection(QueryBuilder $queryBuilder, Request $request, $route, array $routeParams = [])
    {
        $page = $request->query->get('page', 1);

        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(10);
        $pagerfanta->setCurrentPage($page);

        $users = [];
        foreach ($pagerfanta->getCurrentPageResults() as $result) {
            $users[] = $result;
        }

        $paginatedCollection = new PaginatedCollection($users, $pagerfanta->getNbResults());

        $createLinkUrl = function($targetPage) use ($route, $routeParams) {
            return $this->urlGenerator->generate($route, array_merge(
                $routeParams,
                ['page' => $targetPage]
            ));
        };

        $paginatedCollection->addLink('self', $createLinkUrl($page));
        $paginatedCollection->addLink('first', $createLinkUrl(1));
        $paginatedCollection->addLink('last', $createLinkUrl($pagerfanta->getNbPages()));

        if ($pagerfanta->hasNextPage()) {
            $paginatedCollection->addLink('next', $createLinkUrl($pagerfanta->getNextPage()));
        }

        if ($pagerfanta->hasPreviousPage()) {
            $paginatedCollection->addLink('prev', $createLinkUrl($pagerfanta->getPreviousPage()));
        }

        return $paginatedCollection;
    }
}
