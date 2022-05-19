<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceStream\CrossReferenceStreamType;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParser;
use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Document\Object\ObjectStream\ObjectStreamParser;

class ObjectParser
{
    /**
     * @return array<PDFObject>
     * @throws \PrinsFrank\PdfParser\Exception\ParseFailureException
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
        $objects = [];
        foreach ($byteOffsets as $byteOffset) {
            $object = new PDFObject();
            $object->setContent(substr($document->content, $previousByteOffset, $byteOffset - $previousByteOffset));
            $object->setDictionary(DictionaryParser::parse($object->content));
            $object->setDecodedStream(ObjectStreamParser::parse($object->content, $object->dictionary));
            $objects[] = $object;
            $previousByteOffset = $byteOffset;
        }

        return $objects;
    }
}
