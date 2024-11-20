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
        return (string)(float) $value === $value;
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
