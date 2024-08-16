<?php

namespace ATSearchBundle\Search\Converter;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use ATSearchBundle\Query\AggregationQuery;

#[AutoconfigureTag('at_search.search.query.aggregation_converter')]
interface AggregationInputQueryToSearchQueryConverterInterface
{
    public function convert(AggregationQuery $aggregationQuery, InputQueryToSearchAggregation $converter): array;

    public function supports(AggregationQuery $aggregationQuery): bool;
}