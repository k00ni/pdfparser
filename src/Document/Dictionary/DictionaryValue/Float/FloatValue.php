<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Float;

use Override;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValue;
use PrinsFrank\PdfParser\Exception\InvalidDictionaryValueTypeFormatException;

class FloatValue implements DictionaryValue {
    public function __construct(
        public readonly float $value
    ) {
    }

    #[Override]
    public static function acceptsValue(string $value): bool {
        return number_format((float) $value, (int) strpos(strrev($value), ".")) === $value;
    }

    #[Override]
    public static function fromValue(string $valueString): self {
        $valueAsFloat = (float) $valueString;
        if (number_format($valueAsFloat, (int) strpos(strrev($valueString), ".")) !== $valueString) {
            throw new InvalidDictionaryValueTypeFormatException('Non numerical value encountered for floatValue: "' . $valueString . '"');
        }

        return new self($valueAsFloat);
    }
}
