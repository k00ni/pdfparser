<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\ObjectStream;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceStream\CrossReferenceStreamType;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParser;
use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Document\Object\ObjectStream\ObjectStreamContent\ObjectStreamContentParser;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

class ObjectStreamParser
{
    /**
     * @return array<ObjectStream>
     * @throws ParseFailureException
     */
    public static function parse(Document $document): array
    {
        $byteOffsets = [$document->contentLength];
        foreach ($document->crossReferenceSource->data as $crossReferenceData) {
            if ($crossReferenceData->type === CrossReferenceStreamType::TYPE_UNCOMPRESSED_OBJECT) {
                $byteOffsets[] = hexdec($crossReferenceData->objNumberOrByteOffset);
            }
        }

        $previousByteOffset = 0;
        sort($byteOffsets);
        $objectStreams = [];
        foreach ($byteOffsets as $byteOffset) {
            $objectStream = new ObjectStream();
            $objectStream->setContent(substr($document->content, $previousByteOffset, $byteOffset - $previousByteOffset));
            $objectStream->setDictionary(DictionaryParser::parse($objectStream->content));
            $objectStream->setDecodedStream(ObjectStreamContentParser::parse($objectStream->content, $objectStream->dictionary));
            $objectStreams[] = $objectStream;
            $previousByteOffset = $byteOffset;
        }

        return $objectStreams;
    }
}
