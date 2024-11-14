<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Float;

use Override;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\DictionaryValueType;
use PrinsFrank\PdfParser\Exception\InvalidDictionaryValueTypeFormatException;

class FloatValue implements DictionaryValueType {
    public function __construct(
        public readonly float $value
    ) {
    }

    #[Override]
    public static function fromValue(string $valueString): self {
        $valueAsInt = (float) $valueString;
        if ((string) $valueAsInt !== $valueString) {
            throw new InvalidDictionaryValueTypeFormatException('Non numerical value encountered for floatValue: "' . $valueString . '"');
        }

        return new self($valueAsInt);
    }
}
