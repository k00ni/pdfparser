<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Page;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Reference\ReferenceValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Reference\ReferenceValueArray;
use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Exception\InvalidArgumentException;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

class PageCollectionParser
{
    /**
     * @throws ParseFailureException
     * @throws InvalidArgumentException
     */
    public static function parse(Document $document): PageCollection
    {
        $xRefStreams = $document->objectStreamCollection->getObjectStreamsByType(TypeNameValue::X_REF);
        if (count($xRefStreams) !== 1) {
            throw new ParseFailureException('Expected 1 xrefStream, "' . count($xRefStreams) . '" retrieved');
        }

        $rootObjectReference = $xRefStreams[0]->dictionary->getEntryWithKey(DictionaryKey::ROOT)?->value;
        if ($rootObjectReference instanceof ReferenceValue === false) {
            throw new ParseFailureException('Unable to retrieve ROOT crossReference');
        }

        $rootObject = $document->objectStreamCollection->getObjectByReference($rootObjectReference);
        if ($rootObject === null) {
            throw new ParseFailureException('Root object was not found');
        }

        $pagesObjectReference = $rootObject->dictionary->getEntryWithKey(DictionaryKey::PAGES)?->value;
        if ($pagesObjectReference instanceof ReferenceValue === false) {
            throw new ParseFailureException('Pages object reference was not found');
        }

        $pagesObject = $document->objectStreamCollection->getObjectByReference($pagesObjectReference);
        if ($pagesObject === null) {
            throw new ParseFailureException('Pages object was not found');
        }

        $pagesKids = $pagesObject->dictionary->getEntryWithKey(DictionaryKey::KIDS)?->value;
        if ($pagesKids instanceof ReferenceValueArray === false) {
            throw new ParseFailureException('Expected array of reference values for kids, none returned.');
        }

        $pages = [];
        foreach ($pagesKids->referenceValues as $referenceValue) {
            $pageObject = $document->objectStreamCollection->getObjectByReference($referenceValue);
            if ($pageObject === null) {
                throw new ParseFailureException('Object with reference "' . $referenceValue . '" not found');
            }

            $contentReference = $pageObject->dictionary->getEntryWithKey(DictionaryKey::CONTENTS)?->value;
            if ($contentReference instanceof ReferenceValue === false && $contentReference instanceof ReferenceValueArray === false) {
                throw new ParseFailureException('Expected a reference to the contents of a page, none found');
            }

            $contentObjects = $document->objectStreamCollection->getObjectsByReference($contentReference);
            if ($contentObjects === []) {
                throw new ParseFailureException('Object for content with reference "' . $contentReference->objectNumber . ':' . $contentReference->versionNumber . '" not found');
            }

            $pages[] = new Page($pageObject, ...$contentObjects);
        }

        return new PageCollection(...$pages);
    }
}
