<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document;

use PrinsFrank\PdfParser\Document\CrossReference\Source\CrossReferenceSource;
use PrinsFrank\PdfParser\Document\CrossReference\Source\Section\SubSection\Entry\CrossReferenceEntryCompressed;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Object\Decorator\Catalog;
use PrinsFrank\PdfParser\Document\Object\Decorator\DecoratedObjectFactory;
use PrinsFrank\PdfParser\Document\Object\Decorator\DecoratedObject;
use PrinsFrank\PdfParser\Document\Object\Decorator\Page;
use PrinsFrank\PdfParser\Document\Object\Item\UncompressedObject\UncompressedObject;
use PrinsFrank\PdfParser\Document\Object\Item\UncompressedObject\UncompressedObjectParser;
use PrinsFrank\PdfParser\Document\Version\Version;
use PrinsFrank\PdfParser\Exception\ParseFailureException;
use PrinsFrank\PdfParser\Exception\RuntimeException;
use PrinsFrank\PdfParser\Stream;

final class Document {
    /** @var list<Page> */
    private readonly array $pages;

    public function __construct(
        public readonly Stream               $stream,
        public readonly Version              $version,
        public readonly CrossReferenceSource $crossReferenceSource,
    ) {
    }

    public function getInformationDictionary(): ?DecoratedObject {
        $infoReference = $this->crossReferenceSource->getReferenceForKey(DictionaryKey::INFO);
        if ($infoReference === null) {
            return null;
        }

        return $this->getObject($infoReference->objectNumber);
    }

    public function getCatalog(): Catalog {
        $rootReference = $this->crossReferenceSource->getReferenceForKey(DictionaryKey::ROOT)
            ?? throw new ParseFailureException('Unable to locate root for document.');
        $catalog = $this->getObject($rootReference->objectNumber, TypeNameValue::CATALOG)
            ?? throw new ParseFailureException(sprintf('Document references object %d as root, but object couln\'t be located', $rootReference->objectNumber));
        if (!$catalog instanceof Catalog) {
            throw new RuntimeException('Catalog should be a catalog item');
        }

        return $catalog;
    }

    public function getObject(int $objectNumber, ?TypeNameValue $expectedType = null): ?DecoratedObject {
        $crossReferenceEntry = $this->crossReferenceSource->getCrossReferenceEntry($objectNumber);
        if ($crossReferenceEntry === null) {
            return null;
        }

        if ($crossReferenceEntry instanceof CrossReferenceEntryCompressed) {
            $parentObject = $this->getObject($crossReferenceEntry->storedInStreamWithObjectNumber, null)
                ?? throw new RuntimeException(sprintf('Parent object for %d with number %d doesn\'t exist', $objectNumber, $crossReferenceEntry->storedInStreamWithObjectNumber));
            if (!$parentObject->objectItem instanceof UncompressedObject) {
                throw new RuntimeException('Parents for stream items shouldn\'t be stream items themselves');
            }

            return DecoratedObjectFactory::forItem(
                $parentObject->objectItem->getCompressedObject($objectNumber, $this->stream),
                $this->stream,
                $expectedType,
            );
        }

        return DecoratedObjectFactory::forItem(
            UncompressedObjectParser::parseObject($crossReferenceEntry, $objectNumber, $this->stream, ),
            $this->stream,
            $expectedType,
        );
    }

    public function getPage(int $pageNumber): ?Page {
        return $this->getPages()[$pageNumber - 1] ?? null;
    }

    public function getNumberOfPages(): int {
        return count($this->getPages());
    }

    /** @return list<Page> */
    public function getPages(): array {
        return $this->pages ??= $this->getCatalog()
            ->getPagesRoot($this)
            ->getPageItems($this);
    }
}
