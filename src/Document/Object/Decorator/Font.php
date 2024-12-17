<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\Decorator;

use Override;
use PrinsFrank\PdfParser\Document\CMap\ToUnicode\ToUnicodeCMap;
use PrinsFrank\PdfParser\Document\CMap\ToUnicode\ToUnicodeCMapParser;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Array\ArrayValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Integer\IntegerValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Reference\ReferenceValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\TextString\TextStringValue;
use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Document\Object\Item\UncompressedObject\UncompressedObject;
use PrinsFrank\PdfParser\Exception\ParseFailureException;
use PrinsFrank\PdfParser\Stream;

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

    public function getToUnicodeCMap(Document $document): ?ToUnicodeCMap {
        $toUnicodeObject = $this->getDictionary($this->stream)
            ->getObjectForReference($document, DictionaryKey::TO_UNICODE);
        if ($toUnicodeObject === null) {
            return null;
        }

        if ($toUnicodeObject->objectItem instanceof UncompressedObject === false) {
            throw new ParseFailureException();
        }

        return ToUnicodeCMapParser::parse(($stream = Stream::fromString($toUnicodeObject->objectItem->getStreamContent($document->stream))), 0, $stream->getSizeInBytes());
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

    /** @return array<mixed>|null */
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
