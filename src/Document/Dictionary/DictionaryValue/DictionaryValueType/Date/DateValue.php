<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Date;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\DictionaryValueType;

class DateValue implements DictionaryValueType
{
    public function __construct(public readonly string $value) { }

    public static function fromValue(string $valueString): DictionaryValueType
    {
        return new self($valueString);
    }
}
