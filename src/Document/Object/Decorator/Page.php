<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\Decorator;

use Override;
use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Object\Item\UncompressedObject\UncompressedObject;
use PrinsFrank\PdfParser\Document\Text\TextObjectCollection;
use PrinsFrank\PdfParser\Document\Text\TextParser;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

class Page extends DecoratedObject {
    public function getText(): string {
        return implode(' ', array_map(
            fn (TextObjectCollection $textObjectCollection) => $textObjectCollection->getText($this->document, $this),
            $this->getTextObjectCollections(),
        ));
    }

    /** @return list<TextObjectCollection> */
    public function getTextObjectCollections(): array {
        return array_map(
            function (DecoratedObject $decoratedObject) {
                if (!($objectItem = $decoratedObject->objectItem) instanceof UncompressedObject) {
                    throw new ParseFailureException();
                }

                return TextParser::parse($objectItem->getStreamContent($this->document->stream));
            },
            $this->document->getObjectsByDictionaryKey($this->getDictionary(), DictionaryKey::CONTENTS),
        );
    }

    public function getResourceDictionary(): ?Dictionary {
        return $this->getDictionary()
            ->getSubDictionary($this->document, DictionaryKey::RESOURCES);
    }

    public function getFontDictionary(): ?Dictionary {
        if (($pageFont = $this->getResourceDictionary()?->getSubDictionary($this->document, DictionaryKey::FONT)) !== null) {
            return $pageFont;
        }

        if (($pagesParent = $this->getDictionary()->getObjectForReference($this->document, DictionaryKey::PARENT, Pages::class)) === null) {
            return null;
        }

        return $pagesParent->getResourceDictionary()
            ?->getSubDictionary($this->document, DictionaryKey::FONT);
    }

    #[Override]
    protected function getTypeName(): ?TypeNameValue {
        return TypeNameValue::PAGE;
    }
}
