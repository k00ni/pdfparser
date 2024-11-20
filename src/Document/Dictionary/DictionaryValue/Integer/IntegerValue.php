<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Integer;

use Override;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValue;
use PrinsFrank\PdfParser\Exception\InvalidDictionaryValueTypeFormatException;

class IntegerValue implements DictionaryValue {
    public function __construct(
        public readonly int $value
    ) {
    }

    #[Override]
    public static function acceptsValue(string $value): bool {
        return (string)(int) $value === $value;
    }

    #[Override]
    public static function fromValue(string $valueString): self {
        $valueAsInt = (int) $valueString;
        if ((string) $valueAsInt !== $valueString) {
            throw new InvalidDictionaryValueTypeFormatException('Non numerical value encountered for integerValue: "' . $valueString . '"');
        }

        return new self($valueAsInt);
    }
}
