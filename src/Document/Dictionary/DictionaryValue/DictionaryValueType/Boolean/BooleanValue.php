<?php

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Boolean;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\DictionaryValueType;
use PrinsFrank\PdfParser\Exception\InvalidArgumentException;

class BooleanValue implements DictionaryValueType
{
    public function __construct(
        public readonly bool $value,
    ){
    }

    public static function fromValue(string $valueString): DictionaryValueType {
        if ($valueString === 'true') {
            return new self(true);
        }

        if ($valueString === 'false') {
            return new self(false);
        }

        throw new InvalidArgumentException(sprintf('"%s" is not a valid boolean value', $valueString));
    }
}