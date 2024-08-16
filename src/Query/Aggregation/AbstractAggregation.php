<?php

namespace ATSearchBundle\Query\Aggregation;

use ATSearchBundle\Query\AggregationQuery;

abstract readonly class AbstractAggregation implements AggregationQuery
{
    public function __construct(private string $name)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }
}