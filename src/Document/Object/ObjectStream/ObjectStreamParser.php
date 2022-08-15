<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\ObjectStream;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceStream\CrossReferenceStreamType;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable\CrossReferenceTable;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParser;
use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Document\Object\ObjectParser;
use PrinsFrank\PdfParser\Document\Object\ObjectStream\ObjectStreamContent\ObjectStreamContentParser;
use PrinsFrank\PdfParser\Exception\ParseFailureException;
use Throwable;

class ObjectStreamParser
{
    /**
     * @return array<ObjectStream>
     * @throws ParseFailureException
     */
    public static function parse(Document $document): array
    {
        $byteOffsets = [$document->contentLength];
        if ($document->crossReferenceSource instanceof CrossReferenceTable) {
            foreach ($document->crossReferenceSource->crossReferenceSubSections as $crossReferenceSubSection) {
                foreach ($crossReferenceSubSection->crossReferenceEntries as $crossReferenceEntry) {
                    $byteOffsets[] = $crossReferenceEntry->offset;
                }
            }
        } else {
            foreach ($document->crossReferenceSource->data as $crossReferenceData) {
                if ($crossReferenceData->type === CrossReferenceStreamType::TYPE_UNCOMPRESSED_OBJECT) {
                    $byteOffsets[] = hexdec($crossReferenceData->objNumberOrByteOffset);
                }
            }
        }

        $previousByteOffset = 0;
        sort($byteOffsets);
        $objectStreams = [];
        foreach ($byteOffsets as $byteOffset) {
            $objectStream = new ObjectStream();
            $objectStream->setContent(substr($document->content, $previousByteOffset, $byteOffset - $previousByteOffset));
            $objectStream->setDictionary(DictionaryParser::parse($document, $objectStream->content));
            $objectStream->setDecodedStream(ObjectStreamContentParser::parse($objectStream->content, $objectStream->dictionary));
            $objectStream->setObjects(...ObjectParser::parse($document, $objectStream));
            $objectStreams[] = $objectStream;
            $previousByteOffset = $byteOffset;
        }

        return $objectStreams;
    }
}
