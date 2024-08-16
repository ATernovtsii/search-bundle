<?php

namespace ATSearchBundle\Query\Aggregation;

use ATSearchBundle\Enum\DateHistogramIntervalType;

readonly class DateHistogramAggregation extends HistogramAggregation
{
    public function __construct(
        string $name,
        string $field,
        string $interval,
        ?string $order = null,
        string $sortField = '_key',
        array $aggregations = [],
        public DateHistogramIntervalType $intervalType = DateHistogramIntervalType::CALENDAR_INTERVAL,
    ) {
        parent::__construct($name, $field, $interval, $order, $sortField, $aggregations);
    }
}