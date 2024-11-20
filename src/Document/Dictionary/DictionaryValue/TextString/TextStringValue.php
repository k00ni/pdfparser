<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\TextString;

use Override;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValue;

class TextStringValue implements DictionaryValue {
    public function __construct(
        public readonly string $textStringValue
    ) {
    }

    #[Override]
    public static function acceptsValue(string $value): bool {
        return true;
    }

    #[Override]
    public static function fromValue(string $valueString): self {
        return new self($valueString);
    }
}
