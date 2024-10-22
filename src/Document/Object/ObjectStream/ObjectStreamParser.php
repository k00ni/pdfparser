<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\ObjectStream;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceStream\Entry\UncompressedDataEntry;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable\CrossReferenceTable;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParser;
use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Document\Object\ObjectParser;
use PrinsFrank\PdfParser\Document\Object\ObjectStream\ObjectStreamContent\ObjectStreamContentParser;
use PrinsFrank\PdfParser\Exception\BufferTooSmallException;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

class ObjectStreamParser {
    /** @throws ParseFailureException|BufferTooSmallException */
    public static function parse(Document $document): ObjectStreamCollection {
        $byteOffsets = [$document->contentLength];
        if ($document->crossReferenceSource instanceof CrossReferenceTable) {
            foreach ($document->crossReferenceSource->crossReferenceSubSections as $crossReferenceSubSection) {
                foreach ($crossReferenceSubSection->crossReferenceEntries as $crossReferenceEntry) {
                    $byteOffsets[] = $crossReferenceEntry->offset;
                }
            }
        } else {
            foreach ($document->crossReferenceSource->entries as $crossReferenceData) {
                if ($crossReferenceData instanceof UncompressedDataEntry) {
                    $byteOffsets[] = $crossReferenceData->objNumberOrByteOffset;
                }
            }
        }

        sort($byteOffsets);
        if (count($byteOffsets) === 1) {
            $document->errorCollection->addError('Only 1 byte offset was retrieved.');
        }

        $previousByteOffset = 0;
        $objectStreams = [];
        foreach ($byteOffsets as $byteOffset) {
            $objectStream = new ObjectStream();
            $firstLine = substr($document->content, $previousByteOffset, strpos($document->content, "\n", $previousByteOffset) - $previousByteOffset);
            $objectIndicators = explode(' ', $firstLine);
            if (count($objectIndicators) === 3 && $objectIndicators[2] === 'obj') {
                $objectStream->setObjectId((int)$objectIndicators[0]);
                $objectStream->setGenerationNumber((int) $objectIndicators[1]);
            }
            $objectStream->setContent(substr($document->content, $previousByteOffset, $byteOffset - $previousByteOffset));
            $objectStream->setDictionary(DictionaryParser::parse($objectStream->content, $document->errorCollection));
            $objectStream->setDecodedStream(ObjectStreamContentParser::parse($objectStream->content, $objectStream->dictionary));
            $objectStream->setObjectItemCollection(ObjectParser::parse($objectStream, $document->errorCollection));
            $objectStreams[] = $objectStream;
            $previousByteOffset = $byteOffset;
        }

        return new ObjectStreamCollection(...$objectStreams);
    }
}
