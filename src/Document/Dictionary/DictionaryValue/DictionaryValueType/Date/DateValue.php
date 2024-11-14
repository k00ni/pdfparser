<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Date;

use Override;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\DictionaryValueType;

class DateValue implements DictionaryValueType {
    public function __construct(
        public readonly string $value
    ) {
    }

    #[Override]
    public static function fromValue(string $valueString): self {
        return new self($valueString);
    }
}
