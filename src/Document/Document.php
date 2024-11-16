<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document;

use PrinsFrank\PdfParser\Document\CrossReference\Source\CrossReferenceSource;
use PrinsFrank\PdfParser\Document\CrossReference\Source\Section\SubSection\Entry\CrossReferenceEntryCompressed;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Reference\ReferenceValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Reference\ReferenceValueArray;
use PrinsFrank\PdfParser\Document\Object\ObjectItem;
use PrinsFrank\PdfParser\Document\Object\UncompressedObject\UncompressedObject;
use PrinsFrank\PdfParser\Document\Object\UncompressedObject\UncompressedObjectParser;
use PrinsFrank\PdfParser\Document\Version\Version;
use PrinsFrank\PdfParser\Exception\InvalidArgumentException;
use PrinsFrank\PdfParser\Exception\ParseFailureException;
use PrinsFrank\PdfParser\Exception\RuntimeException;
use PrinsFrank\PdfParser\Stream;

final class Document {
    /** @var list<ObjectItem> */
    private readonly array $pages;

    public function __construct(
        public readonly Stream               $stream,
        public readonly Version              $version,
        public readonly CrossReferenceSource $crossReferenceSource,
    ) {
    }

    public function getInformationDictionary(): ?ObjectItem {
        $infoReference = $this->crossReferenceSource->getReferenceForKey(DictionaryKey::INFO);
        if ($infoReference === null) {
            return null;
        }

        return $this->getObject($infoReference->objectNumber);
    }

    public function getCatalog(): ObjectItem {
        $rootReference = $this->crossReferenceSource->getReferenceForKey(DictionaryKey::ROOT)
            ?? throw new ParseFailureException('Unable to locate root for document.');

        return $this->getObject($rootReference->objectNumber)
            ?? throw new ParseFailureException(sprintf('Document references object %d as root, but object couln\'t be located', $rootReference->objectNumber));
    }

    public function getObject(int $objectNumber): ?ObjectItem {
        $crossReferenceEntry = $this->crossReferenceSource->getCrossReferenceEntry($objectNumber);
        if ($crossReferenceEntry === null) {
            return null;
        }

        if ($crossReferenceEntry instanceof CrossReferenceEntryCompressed) {
            $parentObject = $this->getObject($crossReferenceEntry->storedInStreamWithObjectNumber) ?? throw new RuntimeException(sprintf('Parent object for %d with number %d doesn\'t exist', $objectNumber, $crossReferenceEntry->storedInStreamWithObjectNumber));
            if (!$parentObject instanceof UncompressedObject) {
                throw new RuntimeException('Parents for stream items shouldn\'t be stream items themselves');
            }

            return $parentObject->getCompressedObject($objectNumber, $this->stream);
        }

        return UncompressedObjectParser::parseObject(
            $crossReferenceEntry,
            $objectNumber,
            $this->stream,
        );
    }

    public function getPage(int $pageNumber): ?ObjectItem {
        return $this->getPages()[$pageNumber - 1] ?? null;
    }

    public function getNumberOfPages(): int {
        return count($this->getPages());
    }

    /** @return list<ObjectItem> */
    public function getPages(): array {
        if (isset($this->pages)) {
            return $this->pages;
        }

        return $this->pages = $this->getKidsForPages(
            $this->getPagesRoot()
        );
    }

    /** @return list<ObjectItem> */
    public function getKidsForPages(ObjectItem $object): array {
        $dictionary = $object->getDictionary($this->stream);
        if (($type = $dictionary?->getValueForKey(DictionaryKey::TYPE, TypeNameValue::class)) !== TypeNameValue::PAGES) {
            throw new InvalidArgumentException(sprintf('Kids for pages can only be retrieved for pages object, got %s', $type->name ?? 'Unknown'));
        }

        $kids = [];
        foreach ($dictionary->getValueForKey(DictionaryKey::KIDS, ReferenceValueArray::class)->referenceValues ?? [] as $referenceValue) {
            $kidObject = $this->getObject($referenceValue->objectNumber)
                ?? throw new ParseFailureException(sprintf('Child with number %d could not be found', $referenceValue->objectNumber));
            $objectDictionary = $kidObject->getDictionary($this->stream);
            if (($type = $objectDictionary?->getValueForKey(DictionaryKey::TYPE, TypeNameValue::class)) === TypeNameValue::PAGES) {
                $kids = [...$kids, ...$this->getKidsForPages($kidObject)];
            } elseif ($type === TypeNameValue::PAGE) {
                $kids[] = $kidObject;
            } else {
                throw new RuntimeException(sprintf('Expected only nodes of PAGE or PAGES, got %s', $type?->name));
            }
        }

        return $kids;
    }

    public function getPagesRoot(): ObjectItem {
        $catalogDictionary = $this->getCatalog()->getDictionary($this->stream)
            ?? throw new RuntimeException('Unable to retrieve catalog dictionary');
        $pagesReference = $catalogDictionary->getValueForKey(DictionaryKey::PAGES, ReferenceValue::class)
            ?? throw new ParseFailureException('Every catalog dictionary should contain a pages reference, none found');

        return $this->getObject($pagesReference->objectNumber)
            ?? throw new ParseFailureException(sprintf('Unable to retrieve pages root object with number %d', $pagesReference->objectNumber));
    }
}
