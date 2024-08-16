<?php

namespace ATSearchBundle\Search\Converter\AggregationQuery;

use ATSearchBundle\Query\Aggregation\TermsAggregation;
use ATSearchBundle\Query\AggregationQuery;
use ATSearchBundle\Search\Converter\AggregationInputQueryToSearchQueryConverterInterface;
use ATSearchBundle\Search\Converter\InputQueryToSearchAggregation;
use ATSearchBundle\Search\Resolver\FieldNameResolver;
use InvalidArgumentException;

readonly class TermsConverterAggregation implements AggregationInputQueryToSearchQueryConverterInterface
{
    public function __construct(private FieldNameResolver $fieldNameResolver)
    {
    }

    public function convert(AggregationQuery $aggregationQuery, InputQueryToSearchAggregation $converter): array
    {
        if (!$aggregationQuery instanceof TermsAggregation) {
            throw new InvalidArgumentException('Unsupported aggregation');
        }
        $ESFieldName = $this->fieldNameResolver->resolve($aggregationQuery->field) ?? $aggregationQuery->field;

        $payload = [
            'field' => $ESFieldName,
            'size' => $aggregationQuery->size,
        ];

        if ($aggregationQuery->order) {
            $payload['order'] = [$aggregationQuery->sortField => $aggregationQuery->order];
        }

        if ($aggregationQuery->aggregations) {
            $payload['aggs'] = $converter->convert($aggregationQuery->aggregations);
        }

        return ['terms' => $payload];
    }

    public function supports(AggregationQuery $aggregationQuery): bool
    {
        return $aggregationQuery instanceof TermsAggregation;
    }
}