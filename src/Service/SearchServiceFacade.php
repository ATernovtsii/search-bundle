<?php

namespace ATSearchBundle\Service;

use ATSearchBundle\Doctrine\Service\SearchService as DoctrineSearchServiceAlias;
use ATSearchBundle\Elastic\Service\SearchService as ElasticSearchServiceAlias;
use ATSearchBundle\Enum\SearchSourceEnum;
use ATSearchBundle\Query\SearchQuery;
use ATSearchBundle\ValueObject\Result;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

#[AsAlias(SearchServiceInterface::class)]
readonly class SearchServiceFacade implements SearchServiceInterface
{
    public function __construct(
        private ElasticSearchServiceAlias $elasticSearchService,
        private DoctrineSearchServiceAlias $doctrineSearchService
    ) {
    }

    public function searchBySearchQuery(SearchQuery $searchQuery): Result
    {
        if ($searchQuery->searchSource === SearchSourceEnum::ELASTIC) {
            return $this->elasticSearchService->searchBySearchQuery($searchQuery);
        }

        return $this->doctrineSearchService->searchBySearchQuery($searchQuery);
    }
}