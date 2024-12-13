<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\Decorator;

use Override;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Array\ArrayValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Integer\IntegerValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Reference\ReferenceValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\TextString\TextStringValue;

class Font extends DecoratedObject {
    public function getBaseFont(): ?string {
        return $this->getDictionary($this->stream)
            ->getValueForKey(DictionaryKey::BASE_FONT, TextStringValue::class)
            ?->textStringValue;
    }

    public function getEncoding(): ?string {
        return $this->getDictionary($this->stream)
            ->getValueForKey(DictionaryKey::ENCODING, TextStringValue::class)
            ?->textStringValue;
    }

    public function getToUnicode(): string {
        return $this->getDictionary($this->stream)
            ->getValueForKey(DictionaryKey::TO_UNICODE, TextStringValue::class)
            ?->textStringValue;
    }

    public function getFirstChar(): ?int {
        return $this->getDictionary($this->stream)
            ->getValueForKey(DictionaryKey::FIRST_CHAR, IntegerValue::class)
            ?->value;
    }

    public function getLastChar(): ?int {
        return $this->getDictionary($this->stream)
            ->getValueForKey(DictionaryKey::LAST_CHAR, IntegerValue::class)
            ?->value;
    }

    public function getWidths(): ?array {
        return $this->getDictionary($this->stream)
            ->getValueForKey(DictionaryKey::WIDTHS, ArrayValue::class)
            ?->value;
    }

    public function getFontDescriptor(): ?ReferenceValue {
        return $this->getDictionary($this->stream)
            ->getValueForKey(DictionaryKey::FONT_DESCRIPTOR, ReferenceValue::class);
    }

    #[Override]
    protected function getTypeName(): ?TypeNameValue {
        return TypeNameValue::FONT;
    }
}
