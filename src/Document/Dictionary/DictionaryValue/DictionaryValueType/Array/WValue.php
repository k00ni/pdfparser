<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Array;

use Override;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\DictionaryValueType;
use PrinsFrank\PdfParser\Exception\InvalidArgumentException;
use PrinsFrank\PdfParser\Exception\InvalidDictionaryValueTypeFormatException;

class WValue implements DictionaryValueType {
    public function __construct(
        public readonly int $lengthRecord1InBytes,
        public readonly int $lengthRecord2InBytes,
        public readonly int $lengthRecord3InBytes,
    ) {}

    public function getTotalLengthInBytes(): int {
        return $this->lengthRecord1InBytes + $this->lengthRecord2InBytes + $this->lengthRecord3InBytes;
    }

    #[Override]
    public static function fromValue(string $valueString): DictionaryValueType {
        if (str_starts_with($valueString, '[') === false || str_ends_with($valueString, ']') === false) {
            throw new InvalidDictionaryValueTypeFormatException('Invalid value for array: "' . $valueString . '", should start with "[" and end with "]".');
        }

        $values = explode(' ', trim(rtrim(ltrim($valueString, '['), ']')));
        if (count($values) !== 3) {
            throw new InvalidDictionaryValueTypeFormatException(sprintf('Expected exactly 3 integers, got %d', count($values)));
        }

        return new self((int) $values[0], (int) $values[1], (int) $values[2]);
    }
}
