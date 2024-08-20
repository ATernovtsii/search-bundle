<?php

namespace ATSearchBundle\Query\Aggregation;

class RangeAggregation extends AbstractAggregation
{
    public function __construct(
        string $name,
        public string $field,
        public array $ranges,
        public array $aggregations = []
    ) {
        parent::__construct($name);
    }
}