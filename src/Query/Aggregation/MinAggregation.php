<?php

namespace ATSearchBundle\Query\Aggregation;

class MinAggregation extends AbstractAggregation
{
    public function __construct(string $name, public ?string $field = null, public ?string $script = null)
    {
        parent::__construct($name);
    }
}