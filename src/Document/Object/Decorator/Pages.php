<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\Decorator;

use Override;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Reference\ReferenceValueArray;
use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Exception\ParseFailureException;
use PrinsFrank\PdfParser\Exception\RuntimeException;

class Pages extends DecoratedObject {
    #[Override]
    protected function getTypeName(): ?TypeNameValue {
        return TypeNameValue::PAGES;
    }

    /** @return list<Page> */
    public function getPageItems(Document $document): array {
        $kids = [];
        foreach ($this->getDictionary()->getValueForKey(DictionaryKey::KIDS, ReferenceValueArray::class)->referenceValues ?? [] as $referenceValue) {
            $kidObject = $document->getObject($referenceValue->objectNumber)
                ?? throw new ParseFailureException(sprintf('Child with number %d could not be found', $referenceValue->objectNumber));
            if ($kidObject instanceof Pages) {
                $kids = [...$kids, ...$kidObject->getPageItems($document)];
            } elseif ($kidObject instanceof Page) {
                $kids[] = $kidObject;
            } else {
                throw new RuntimeException(sprintf('Expected only nodes of PAGE or PAGES, got %s', $kidObject::class));
            }
        }

        return $kids;
    }
}
