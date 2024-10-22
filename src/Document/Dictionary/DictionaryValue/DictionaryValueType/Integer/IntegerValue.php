<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Integer;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\DictionaryValueType;
use PrinsFrank\PdfParser\Exception\InvalidDictionaryValueTypeFormatException;

class IntegerValue implements DictionaryValueType {
    public function __construct(
        public readonly int $value
    ) {
    }

    public static function fromValue(string $valueString): DictionaryValueType {
        $valueAsInt = (int) $valueString;
        if ((string) $valueAsInt !== $valueString) {
            throw new InvalidDictionaryValueTypeFormatException('Non numerical value encountered for integerValue: "' . $valueString . '"');
        }

        return new self($valueAsInt);
    }
}
