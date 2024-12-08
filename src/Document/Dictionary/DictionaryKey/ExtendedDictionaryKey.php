<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey;

use Override;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Reference\ReferenceValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\TextString\TextStringValue;

class ExtendedDictionaryKey implements DictionaryKeyInterface {
    public function __construct(
        public readonly string $value,
    ) {
    }

    public static function fromKeyString(string $keyString): self {
        return new self(rtrim(ltrim($keyString, '/'), "\n\t "));
    }

    #[Override]
    public function getValueTypes(): array {
        return [ReferenceValue::class, TextStringValue::class];
    }
}
