<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Integer;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\DictionaryValueType;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

class IntegerValue implements DictionaryValueType
{
    public function __construct(public int $value) { }

    public static function fromValue(string $valueString): DictionaryValueType
    {
        $valueAsInt = (int) $valueString;
        if ((string) $valueAsInt !== $valueString) {
            throw new ParseFailureException('Non numerical value encountered for integerValue: "' . $valueString . '"');
        }

        return new self($valueAsInt);
    }
}
