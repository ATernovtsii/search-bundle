<?php

namespace ATSearchBundle\Search\Converter\AggregationQuery;

use ATSearchBundle\Enum\FieldType;
use ATSearchBundle\Query\Aggregation\AverageAggregation;
use ATSearchBundle\Query\Aggregation\CardinalityAggregation;
use ATSearchBundle\Query\Aggregation\MaxAggregation;
use ATSearchBundle\Query\Aggregation\MinAggregation;
use ATSearchBundle\Query\Aggregation\StatsAggregation;
use ATSearchBundle\Query\Aggregation\SumAggregation;
use ATSearchBundle\Query\Aggregation\ValueCountAggregation;
use ATSearchBundle\Query\AggregationQuery;
use ATSearchBundle\Search\Converter\AggregationInputQueryToSearchQueryConverterInterface;
use ATSearchBundle\Search\Converter\InputQueryToSearchAggregation;
use ATSearchBundle\Search\Mapper\SchemaMapper;
use ATSearchBundle\Search\Resolver\FieldNameResolver;
use InvalidArgumentException;

readonly class BasicConverterAggregation implements AggregationInputQueryToSearchQueryConverterInterface
{
    public function __construct(private FieldNameResolver $fieldNameResolver)
    {
    }

    public function convert(AggregationQuery $aggregationQuery, InputQueryToSearchAggregation $converter): array
    {
        if (
            !$aggregationQuery instanceof MaxAggregation
            && !$aggregationQuery instanceof MinAggregation
            && !$aggregationQuery instanceof AverageAggregation
            && !$aggregationQuery instanceof SumAggregation
            && !$aggregationQuery instanceof CardinalityAggregation
            && !$aggregationQuery instanceof StatsAggregation
            && !$aggregationQuery instanceof ValueCountAggregation
        ) {
            throw new InvalidArgumentException('Unsupported aggregation');
        }
        $func = match (true) {
            $aggregationQuery instanceof MaxAggregation => 'max',
            $aggregationQuery instanceof MinAggregation => 'min',
            $aggregationQuery instanceof AverageAggregation => 'avg',
            $aggregationQuery instanceof SumAggregation => 'sum',
            $aggregationQuery instanceof CardinalityAggregation => 'cardinality',
            $aggregationQuery instanceof StatsAggregation => 'stats',
            $aggregationQuery instanceof ValueCountAggregation => 'value_count',
        };

        $payload = [];
        if ($aggregationQuery->field) {
            $ESFieldName = $this->fieldNameResolver->resolve($aggregationQuery->field) ?? $aggregationQuery->field;

            $fieldType = SchemaMapper::getCustomFieldTypeByESFieldName($ESFieldName);
            if ($func !== 'cardinality' && $func !== 'value_count'
                && !in_array($fieldType, [
                    FieldType::INTEGER->value,
                    FieldType::DATE->value,
                    FieldType::FLOAT->value,
                    FieldType::LONG->value,
                    FieldType::DOUBLE->value,
                    FieldType::BOOLEAN->value,
                    FieldType::MULTI_INTEGER->value,
                ], true)
            ) {
                throw new InvalidArgumentException('Unsupported aggregation for field (' . $ESFieldName . ')');
            }

            $payload = [
                'field' => $ESFieldName,
            ];
        } elseif (!$aggregationQuery instanceof StatsAggregation && $aggregationQuery->script) {
            $payload = [
                'script' => ['source' => $aggregationQuery->script],
            ];
        }

        if (!$payload) {
            throw new InvalidArgumentException('Aggregation field or script must be set');
        }

        return [$func => $payload];
    }

    public function supports(AggregationQuery $aggregationQuery): bool
    {
        return $aggregationQuery instanceof MaxAggregation
            || $aggregationQuery instanceof MinAggregation
            || $aggregationQuery instanceof AverageAggregation
            || $aggregationQuery instanceof SumAggregation
            || $aggregationQuery instanceof StatsAggregation
            || $aggregationQuery instanceof CardinalityAggregation
            || $aggregationQuery instanceof ValueCountAggregation;
    }
}