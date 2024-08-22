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
        ];

        if ($aggregationQuery->size) {
            $payload['size'] = $aggregationQuery->size;
        }

        if ($aggregationQuery->minDocCount) {
            $payload['min_doc_count'] = $aggregationQuery->minDocCount;
        }

        if ($aggregationQuery->order) {
            $payload['order'] = [$aggregationQuery->sortField => $aggregationQuery->order];
        }

        $convertedData = ['terms' => $payload];
        if ($aggregationQuery->aggregations) {
            $convertedData['aggs'] = $converter->convert($aggregationQuery->aggregations);
        }

        return $convertedData;
    }

    public function supports(AggregationQuery $aggregationQuery): bool
    {
        return $aggregationQuery instanceof TermsAggregation;
    }
}