<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\Decorator;

use Override;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Reference\ReferenceValue;
use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Document\Object\Item\UncompressedObject\UncompressedObject;
use PrinsFrank\PdfParser\Document\Text\TextObjectCollection;
use PrinsFrank\PdfParser\Document\Text\TextParser;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

class Page extends DecoratedObject {
    public function getText(Document $document): TextObjectCollection {
        $contentRef = $this->getDictionary($document->stream)->getValueForKey(DictionaryKey::CONTENTS, ReferenceValue::class);
        $contentObject = $document->getObject($contentRef->objectNumber) ?? throw new ParseFailureException(sprintf('Unable to locate content object with object number %d', $contentRef->objectNumber));
        if (!$contentObject->objectItem instanceof UncompressedObject) {
            throw new ParseFailureException('Compressed objects containing text are currently not supported');
        }

        return TextParser::parse($contentObject->objectItem->getStreamContent($document->stream));
    }

    #[Override]
    protected function getTypeName(): ?TypeNameValue {
        return TypeNameValue::PAGE;
    }
}
