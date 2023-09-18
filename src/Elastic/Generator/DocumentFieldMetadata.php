<?php

namespace ATSearchBundle\Elastic\Generator;

use ATSearchBundle\Elastic\Converter\FilterInputQueryToElasticConverterInterface;
use ATSearchBundle\Elastic\Mapper\SchemaMapper;
use ATSearchBundle\Enum\FieldType;

readonly class DocumentFieldMetadata
{
    public function __construct(
        private string $fieldName,
        private ?FieldType $type,
        private string $valueResolver,
        private string $originalFieldName,
        private ?string $subFields
    ) {
    }

    public function getFieldNamesForMap(): array
    {
        $fieldName = $this->originalFieldName;
        if ($this->subFields) {
            $fieldName .= '.' . $this->subFields;
        }
        if (!$this->type) {
            return [$fieldName => FilterInputQueryToElasticConverterInterface::IGNORED_FIELD];
        }

        return [$fieldName => $this->fieldName . SchemaMapper::getSuffixByCustomType($this->type->value)];
    }

    public function getFieldNameWithResolver(): ?string
    {
        if (!$this->type) {
            return null;
        }

        return '\'' . $this->fieldName
            . SchemaMapper::getSuffixByCustomType($this->type->value)
            . '\' => ' . $this->valueResolver;
    }

    public function isMulti(): bool
    {
        return in_array($this->type, [FieldType::MULTI_STRING, FieldType::MULTI_INTEGER, FieldType::MULTI_BOOLEAN], true);
    }
}