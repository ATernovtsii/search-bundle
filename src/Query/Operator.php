<?php

namespace ATSearchBundle\Query;

abstract class Operator
{
    public const string EQ = '=';
    public const string GT = '>';
    public const string GTE = '>=';
    public const string LT = '<';
    public const string LTE = '<=';
    public const string IN = 'in';
    public const string BETWEEN = 'between';
    public const string LIKE = 'like';
    public const string EXISTS = 'exists';
}
