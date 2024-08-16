<?php

namespace ATSearchBundle\Search\Converter\AggregationQuery;

use ATSearchBundle\Enum\FieldType;
use ATSearchBundle\Query\Aggregation\RangeAggregation;
use ATSearchBundle\Query\AggregationQuery;
use ATSearchBundle\Search\Converter\AggregationInputQueryToSearchQueryConverterInterface;
use ATSearchBundle\Search\Converter\InputQueryToSearchAggregation;
use ATSearchBundle\Search\Mapper\SchemaMapper;
use ATSearchBundle\Search\Resolver\FieldNameResolver;
use InvalidArgumentException;

readonly class RangeConverterAggregation implements AggregationInputQueryToSearchQueryConverterInterface
{
    public function __construct(private FieldNameResolver $fieldNameResolver)
    {
    }

    public function convert(AggregationQuery $aggregationQuery, InputQueryToSearchAggregation $converter): array
    {
        if (!$aggregationQuery instanceof RangeAggregation) {
            throw new InvalidArgumentException('Unsupported aggregation');
        }
        $ESFieldName = $this->fieldNameResolver->resolve($aggregationQuery->field) ?? $aggregationQuery->field;
        $fieldType = SchemaMapper::getCustomFieldTypeByESFieldName($ESFieldName);
        if (in_array($fieldType, [
            FieldType::STRING->value,
            FieldType::MULTI_STRING->value,
        ], true)
        ) {
            throw new InvalidArgumentException('Unsupported aggregation for field (' . $ESFieldName . ')');
        }
        $payload = [
            'field' => $ESFieldName,
            'ranges' => $aggregationQuery->ranges,
        ];

        if ($aggregationQuery->aggregations) {
            $payload['aggs'] = $converter->convert($aggregationQuery->aggregations);
        }

        return ['range' => $payload];
    }

    public function supports(AggregationQuery $aggregationQuery): bool
    {
        return $aggregationQuery instanceof RangeAggregation;
    }
}