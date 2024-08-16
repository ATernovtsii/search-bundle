<?php

namespace ATSearchBundle\Enum;

enum DateHistogramIntervalType: string
{
    case FIXED_INTERVAL = 'fixed_interval';
    case CALENDAR_INTERVAL = 'calendar_interval';
}
