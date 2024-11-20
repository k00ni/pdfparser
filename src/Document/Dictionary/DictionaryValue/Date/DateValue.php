<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Date;

use Override;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValue;

class DateValue implements DictionaryValue {
    public function __construct(
        public readonly string $value
    ) {
    }

    #[Override]
    public static function acceptsValue(string $value): bool {
        return str_starts_with($value, '(D:') || str_starts_with($value, 'D:');
    }

    #[Override]
    public static function fromValue(string $valueString): self {
        return new self($valueString);
    }
}
