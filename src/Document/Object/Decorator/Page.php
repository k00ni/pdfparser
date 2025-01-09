<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\Decorator;

use Override;
use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Text\TextObjectCollection;
use PrinsFrank\PdfParser\Document\Text\TextParser;

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
            fn (DecoratedObject $decoratedObject) => TextParser::parse($decoratedObject->objectItem->getStreamContent($this->document->stream)),
            $this->document->getObjectsByDictionaryKey($this->getDictionary(), DictionaryKey::CONTENTS),
        );
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
