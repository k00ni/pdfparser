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
        return preg_match('/^\(?D:[0-9]{4,14}[+-][0-9]{2}\'[0-9]{2}\'?\)?$/', $value) === 1;
    }

    #[Override]
    public static function fromValue(string $valueString): self {
        return new self($valueString);
    }
}
