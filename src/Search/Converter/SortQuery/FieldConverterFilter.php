<?php

namespace ATSearchBundle\Search\Converter\SortQuery;

use ATSearchBundle\Exception\CriterionFieldNotIndexedException;
use ATSearchBundle\Query\SortClause\SortByField;
use ATSearchBundle\Query\SortQueryCriterion;
use ATSearchBundle\Search\Converter\{InputQueryToSearchSort, SortInputQueryToSearchQueryConverterInterface};
use ATSearchBundle\Search\Resolver\FieldNameResolver;
use InvalidArgumentException;
use RuntimeException;

readonly class FieldConverterFilter implements SortInputQueryToSearchQueryConverterInterface
{
    public function __construct(private FieldNameResolver $fieldNameResolver)
    {
    }

    public function convert(SortQueryCriterion $sortClause, InputQueryToSearchSort $converter): array
    {
        if (!$sortClause instanceof SortByField) {
            throw new InvalidArgumentException('Unsupported sort criteria');
        }

        $field = $this->fieldNameResolver->resolve($sortClause->field);
        if (!$field) {
            throw new CriterionFieldNotIndexedException($sortClause);
        }

        return [
            $field => ['order' => $this->getDirection($sortClause)],
        ];
    }

    public function supports(SortQueryCriterion $sortClause): bool
    {
        return $sortClause instanceof SortByField;
    }

    protected function getDirection(SortByField $sortClause): string
    {
        return match ($sortClause->direction) {
            'ASC' => 'asc',
            'DESC' => 'desc',
            default => throw new RuntimeException(
                'Invalid sort direction: ' . $sortClause->field . ' ' . $sortClause->direction
            ),
        };
    }
}