<?php

namespace ATSearchBundle\Search\Converter\FilterQuery;

use ATSearchBundle\Exception\CriterionFieldNotIndexedException;
use ATSearchBundle\Query\Filter\CustomFieldFilter;
use ATSearchBundle\Query\Filter\FieldFilter;
use ATSearchBundle\Query\FilterQueryCriterion;
use ATSearchBundle\Query\Operator;
use ATSearchBundle\Search\Converter\FilterInputQueryToSearchQueryConverterInterface;
use ATSearchBundle\Search\Converter\InputQueryToSearchFilter;
use ATSearchBundle\Search\Resolver\FieldNameResolver;
use ATSearchBundle\Search\ValueObject\QueryDSL\ExistQuery;
use InvalidArgumentException;

readonly class FieldExistConverterFilter implements FilterInputQueryToSearchQueryConverterInterface
{
    public function __construct(private FieldNameResolver $fieldNameResolver)
    {
    }

    public function convert(FilterQueryCriterion $criterion, InputQueryToSearchFilter $converter): array
    {
        if (!$criterion instanceof FieldFilter && !$criterion instanceof CustomFieldFilter) {
            throw new InvalidArgumentException('Unsupported criteria');
        }
        if ($criterion instanceof CustomFieldFilter) {
            $ESFieldName = $criterion->field;
        } else {
            $ESFieldName = $this->fieldNameResolver->resolve($criterion->field);
        }

        if (!$ESFieldName) {
            throw new CriterionFieldNotIndexedException($criterion);
        }

        return (new ExistQuery($ESFieldName))->toArray();
    }

    public function supports(FilterQueryCriterion $criteria): bool
    {
        return ($criteria instanceof FieldFilter || $criteria instanceof CustomFieldFilter)
            && $criteria->operator === Operator::EXISTS;
    }
}