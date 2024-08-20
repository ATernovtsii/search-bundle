<?php

namespace ATSearchBundle\Query\Aggregation;

class TermsAggregation extends AbstractAggregation
{
    public function __construct(
        string $name,
        public string $field,
        public int $size,
        public ?string $order = null,
        public string $sortField = '_count',
        public array $aggregations = []
    ) {
        parent::__construct($name);
    }
}