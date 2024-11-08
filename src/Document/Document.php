<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document;

use PrinsFrank\PdfParser\Document\CrossReference\Source\CrossReferenceSource;
use PrinsFrank\PdfParser\Document\CrossReference\Source\Section\SubSection\Entry\CrossReferenceEntryCompressed;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Reference\ReferenceValue;
use PrinsFrank\PdfParser\Document\Object\ObjectItem;
use PrinsFrank\PdfParser\Document\Object\ObjectItemParser;
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

    public function getCatalog(): ?ObjectItem {
        return $this->getObject(
            $this->crossReferenceSource->getRoot()->objectNumber
        );
    }

    public function getObject(int $objectNumber): ?ObjectItem {
        $crossReferenceEntry = $this->crossReferenceSource->getCrossReferenceEntry($objectNumber);
        if ($crossReferenceEntry === null) {
            throw new ParseFailureException(sprintf('No crossReference entry found for object with number %d', $objectNumber));
        }

        if ($crossReferenceEntry instanceof CrossReferenceEntryCompressed) {
            throw new ParseFailureException('Compressed objects are currently not supported');
        }

        return ObjectItemParser::parseObject(
            $crossReferenceEntry,
            $objectNumber,
            $this->crossReferenceSource,
            $this->stream,
        );
    }
}
