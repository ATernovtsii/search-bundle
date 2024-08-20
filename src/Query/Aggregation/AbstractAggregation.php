<?php

namespace ATSearchBundle\Query\Aggregation;

use ATSearchBundle\Query\AggregationQuery;

abstract class AbstractAggregation implements AggregationQuery
{
    public function __construct(private readonly string $name)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }
}