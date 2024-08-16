<?php

namespace ATSearchBundle\Exception;

use ATSearchBundle\Query\AggregationQuery;
use ATSearchBundle\Query\FilterQueryCriterion;
use ATSearchBundle\Query\SortQueryCriterion;
use Exception;

class NoConverterException extends Exception
{
    public function __construct(FilterQueryCriterion|SortQueryCriterion|AggregationQuery $criteria)
    {
        parent::__construct('No converter found for criteria: ' . get_class($criteria));
    }
}