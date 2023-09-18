<?php

namespace ATSearchBundle\Elastic\Resolver;

readonly class FieldNameResolver
{
    public function __construct(private DocumentResolver $documentResolver)
    {
    }

    public function resolve(string $fieldName): ?string
    {
        return $this->documentResolver->getESFieldName($fieldName);
    }
}