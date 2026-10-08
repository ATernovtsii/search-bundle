<?php

namespace ATSearchBundle\Search\ValueObject\QueryDSL;

final readonly class ExistQuery implements QueryDSLInterface
{
    public function __construct(private string $field)
    {
    }

    public function toArray(): array
    {
        return ['exists' => ['field' => $this->field]];
    }
}
