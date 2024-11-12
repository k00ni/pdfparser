<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document;

use PrinsFrank\PdfParser\Document\CrossReference\Source\CrossReferenceSource;
use PrinsFrank\PdfParser\Document\CrossReference\Source\Section\SubSection\Entry\CrossReferenceEntryCompressed;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Object\ObjectItem;
use PrinsFrank\PdfParser\Document\Object\ObjectItemParser;
use PrinsFrank\PdfParser\Document\Object\ObjectStream\ObjectStreamItem;
use PrinsFrank\PdfParser\Document\Version\Version;
use PrinsFrank\PdfParser\Exception\ParseFailureException;
use PrinsFrank\PdfParser\Stream;

final class Document {
    public function __construct(
        public readonly Stream               $stream,
        public readonly Version              $version,
        public readonly CrossReferenceSource $crossReferenceSource,
    ) {
    }

    public function getInformationDictionary(): ObjectItem|ObjectStreamItem|null {
        $infoReference = $this->crossReferenceSource->getReferenceForKey(DictionaryKey::INFO);
        if ($infoReference === null) {
            return null;
        }

        return $this->getObject($infoReference->objectNumber);
    }

    public function getCatalog(): ObjectItem|ObjectStreamItem {
        $rootReference = $this->crossReferenceSource->getReferenceForKey(DictionaryKey::ROOT)
            ?? throw new ParseFailureException('Unable to locate root for document.');

        return $this->getObject($rootReference->objectNumber)
            ?? throw new ParseFailureException(sprintf('Document references object %d as root, but object couln\'t be located', $rootReference->objectNumber));
    }

    public function getObject(int $objectNumber): ObjectItem|ObjectStreamItem|null {
        $crossReferenceEntry = $this->crossReferenceSource->getCrossReferenceEntry($objectNumber);
        if ($crossReferenceEntry === null) {
            throw new ParseFailureException(sprintf('No crossReference entry found for object with number %d', $objectNumber));
        }

        if ($crossReferenceEntry instanceof CrossReferenceEntryCompressed) {
            return $this->getObject($crossReferenceEntry->storedInStreamWithObjectNumber)
                ->getStreamData($this->stream)
                ->getObjectStreamItem($objectNumber);
        }

        return ObjectItemParser::parseObject(
            $crossReferenceEntry,
            $objectNumber,
            $this->stream,
        );
    }
}
