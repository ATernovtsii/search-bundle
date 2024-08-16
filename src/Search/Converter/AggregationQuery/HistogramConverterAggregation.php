<?php

namespace ATSearchBundle\Search\Converter\AggregationQuery;

use ATSearchBundle\Query\Aggregation\DateHistogramAggregation;
use ATSearchBundle\Query\Aggregation\HistogramAggregation;
use ATSearchBundle\Query\AggregationQuery;
use ATSearchBundle\Search\Converter\AggregationInputQueryToSearchQueryConverterInterface;
use ATSearchBundle\Search\Converter\InputQueryToSearchAggregation;
use ATSearchBundle\Search\Resolver\FieldNameResolver;
use InvalidArgumentException;

readonly class HistogramConverterAggregation implements AggregationInputQueryToSearchQueryConverterInterface
{
    public function __construct(private FieldNameResolver $fieldNameResolver)
    {
    }

    public function convert(AggregationQuery $aggregationQuery, InputQueryToSearchAggregation $converter): array
    {
        if (!$aggregationQuery instanceof HistogramAggregation) {
            throw new InvalidArgumentException('Unsupported aggregation');
        }
        $ESFieldName = $this->fieldNameResolver->resolve($aggregationQuery->field) ?? $aggregationQuery->field;
        if ($aggregationQuery instanceof DateHistogramAggregation) {
            $aggregationType = 'date_histogram';
            $intervalType = $aggregationQuery->intervalType->value;
        } else {
            $aggregationType = 'histogram';
            $intervalType = 'interval';
        }

        $payload = [
            'field' => $ESFieldName,
            $intervalType => $aggregationQuery->interval,
        ];

        if ($aggregationQuery->order) {
            $payload['order'] = [$aggregationQuery->sortField => $aggregationQuery->order];
        }

        $convertedData = [$aggregationType => $payload];
        if ($aggregationQuery->aggregations) {
            $convertedData['aggs'] = $converter->convert($aggregationQuery->aggregations);
        }

        return $convertedData;
    }

    public function supports(AggregationQuery $aggregationQuery): bool
    {
        return $aggregationQuery instanceof HistogramAggregation;
    }
}