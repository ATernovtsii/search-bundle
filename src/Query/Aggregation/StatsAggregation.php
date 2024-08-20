<?php

namespace ATSearchBundle\Query\Aggregation;

class StatsAggregation extends AbstractAggregation
{
    public function __construct(string $name, public string $field)
    {
        parent::__construct($name);
    }
}