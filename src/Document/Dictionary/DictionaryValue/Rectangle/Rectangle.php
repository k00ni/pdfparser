<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Rectangle;

use Override;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValue;

class Rectangle implements DictionaryValue {
    public function __construct(
        public readonly float $xTopLeft,
        public readonly float $yTopLeft,
        public readonly float $xBottomRight,
        public readonly float $yBottomRight
    ) {
    }

    #[Override]
    public static function acceptsValue(string $value): bool {
        return str_starts_with($value, '[') && str_ends_with($value, ']') && count(explode(' ', trim(rtrim(ltrim($value, '['), ']')))) === 4;
    }

    #[Override]
    public static function fromValue(string $valueString): self {
        return new self(... array_map('floatval', explode(' ', trim(rtrim(ltrim($valueString, '['), ']')))));
    }
}
