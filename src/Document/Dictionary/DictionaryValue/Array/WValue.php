<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Array;

use Override;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValue;
use PrinsFrank\PdfParser\Exception\InvalidDictionaryValueTypeFormatException;
use PrinsFrank\PdfParser\Exception\RuntimeException;

class WValue implements DictionaryValue {
    public function __construct(
        public readonly int $lengthRecord1InBytes,
        public readonly int $lengthRecord2InBytes,
        public readonly int $lengthRecord3InBytes,
    ) {
    }

    /** @return int<1, max> */
    public function getTotalLengthInBytes(): int {
        $totalLength = $this->lengthRecord1InBytes + $this->lengthRecord2InBytes + $this->lengthRecord3InBytes;
        if ($totalLength < 1) {
            throw new RuntimeException(sprintf('Total length should not be less than 1, got %d', $totalLength));
        }

        return $totalLength;
    }

    #[Override]
    public static function acceptsValue(string $value): bool {
        return str_starts_with($value, '[') && str_ends_with($value, ']');
    }

    #[Override]
    public static function fromValue(string $valueString): self {
        if (!self::acceptsValue($valueString)) {
            throw new InvalidDictionaryValueTypeFormatException('Invalid value for array: "' . $valueString . '", should start with "[" and end with "]".');
        }

        $values = explode(' ', trim(rtrim(ltrim($valueString, '['), ']')));
        if (count($values) !== 3) {
            throw new InvalidDictionaryValueTypeFormatException(sprintf('Expected exactly 3 integers, got %d', count($values)));
        }

        return new self((int) $values[0], (int) $values[1], (int) $values[2]);
    }
}
