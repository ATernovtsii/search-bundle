<?php

namespace ATSearchBundle\Query\Aggregation;

class HistogramAggregation extends AbstractAggregation
{
    public function __construct(
        string $name,
        public string $field,
        public string $interval,
        public ?string $order = null,
        public string $sortField = '_key',
        public array $aggregations = []
    ) {
        parent::__construct($name);
    }
}