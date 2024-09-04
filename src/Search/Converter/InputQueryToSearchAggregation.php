<?php

namespace ATSearchBundle\Search\Converter;

use ATSearchBundle\Query\AggregationQuery;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use ATSearchBundle\Exception\NoConverterException;

class InputQueryToSearchAggregation
{
    /** @var AggregationInputQueryToSearchQueryConverterInterface[] */
    private iterable $converters;

    public function __construct(#[AutowireIterator('at_search.search.query.aggregation_converter')] iterable $converters)
    {
        $this->converters = $converters;
    }

    private function getConverter(AggregationQuery $aggregation): ?AggregationInputQueryToSearchQueryConverterInterface
    {
        foreach ($this->converters as $converter) {
            if ($converter->supports($aggregation)) {
                return $converter;
            }
        }

        return null;
    }

    /**
     * @param AggregationQuery[] $aggregations
     * @throws NoConverterException
     */
    public function convert(array $aggregations): array
    {
        $convertedAggregations = [];
        foreach ($aggregations as $aggregationQuery) {
            $converter = $this->getConverter($aggregationQuery);

            if ($converter === null) {
                throw new NoConverterException($aggregationQuery);
            }

            $convertedAggregations[$aggregationQuery->getName()] = $converter->convert($aggregationQuery, $this);
        }

        return $convertedAggregations;
    }
}