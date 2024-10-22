<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Rectangle;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\DictionaryValueType;

class Rectangle implements DictionaryValueType {
    public function __construct(public float $xTopLeft, public float $yTopLeft, public float $xBottomRight, public float $yBottomRight) {
    }

    public static function fromValue(string $valueString): DictionaryValueType {
        return new self(... array_map('floatval', explode(' ', trim($valueString, '[]'))));
    }
}
