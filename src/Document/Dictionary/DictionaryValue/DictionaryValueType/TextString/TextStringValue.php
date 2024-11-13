<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\TextString;

use Override;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\DictionaryValueType;

class TextStringValue implements DictionaryValueType {
    public function __construct(
        public readonly string $textStringValue
    ) {
    }

    #[Override]
    public static function fromValue(string $valueString): DictionaryValueType {
        return new self($valueString);
    }
}
