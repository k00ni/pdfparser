<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\Decorator;

use Override;
use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Reference\ReferenceValue;
use PrinsFrank\PdfParser\Document\Object\Item\UncompressedObject\UncompressedObject;
use PrinsFrank\PdfParser\Document\Text\TextObjectCollection;
use PrinsFrank\PdfParser\Document\Text\TextParser;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

class Page extends DecoratedObject {
    public function getText(): string {
        return $this->getTextObjectCollection()
            ->getText($this->document, $this);
    }

    public function getTextObjectCollection(): TextObjectCollection {
        $contentRef = $this->getDictionary()->getValueForKey(DictionaryKey::CONTENTS, ReferenceValue::class)
            ?? throw new ParseFailureException(sprintf('No Contents found for page'));
        $contentObject = $this->document->getObject($contentRef->objectNumber)
            ?? throw new ParseFailureException(sprintf('Unable to locate content object with object number %d', $contentRef->objectNumber));
        if (!$contentObject->objectItem instanceof UncompressedObject) {
            throw new ParseFailureException('Compressed objects containing text are currently not supported');
        }

        return TextParser::parse($contentObject->objectItem->getStreamContent($this->document->stream));
    }

    public function getResourceDictionary(): ?Dictionary {
        return $this->getDictionary()
            ->getSubDictionary($this->document, DictionaryKey::RESOURCES);
    }

    public function getFontDictionary(): ?Dictionary {
        return $this->getResourceDictionary()
            ?->getSubDictionary($this->document, DictionaryKey::FONT);
    }

    #[Override]
    protected function getTypeName(): ?TypeNameValue {
        return TypeNameValue::PAGE;
    }
}
