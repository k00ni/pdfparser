<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Rectangle;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\DictionaryValueType;

class Rectangle implements DictionaryValueType {
    public function __construct(
        public readonly float $xTopLeft,
        public readonly float $yTopLeft,
        public readonly float $xBottomRight,
        public readonly float $yBottomRight
    ) {
    }

    public static function fromValue(string $valueString): DictionaryValueType {
        return new self(... array_map('floatval', explode(' ', trim($valueString, '[]'))));
    }
}
