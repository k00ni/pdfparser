<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\ObjectStream;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParser;
use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Document\Generic\Marker;
use PrinsFrank\PdfParser\Document\Object\ObjectParser;
use PrinsFrank\PdfParser\Document\Object\ObjectStream\ObjectStreamContent\ObjectStreamContentParser;
use PrinsFrank\PdfParser\Document\Version\Version;
use PrinsFrank\PdfParser\Exception\BufferTooSmallException;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

class ObjectStreamParser {
    /** @throws ParseFailureException|BufferTooSmallException */
    public static function parse(Document $document): ObjectStreamCollection {
        $byteOffsets = [...$document->crossReferenceSource->getByteOffsets(), $document->contentLength];
        sort($byteOffsets);
        if (count($byteOffsets) === 1) {
            $document->errorCollection->addError(new Error('Only 1 byte offset was retrieved.'));
        }

        $previousByteOffset = strlen(Marker::VERSION->value) + Version::length() + strlen(PHP_EOL);
        $objectStreams = [];
        foreach ($byteOffsets as $index => $byteOffset) {
            $firstLine = substr($document->content, $previousByteOffset, strpos($document->content, "\n", $previousByteOffset) - $previousByteOffset);
            if (($index === 1 || $index === 0) && preg_match('//u', $firstLine) === false) { // If a PDF file contains binary data, the header line shall be immediately followed by a comment line and at least 4 binary characters 7.5.2 TODO: Proper comment handling
                $previousByteOffset = $byteOffset;

                continue;
            }

            $objectIndicators = explode(' ', $firstLine);
            if (count($objectIndicators) !== 3 || $objectIndicators[2] !== 'obj') {
                throw new \Exception();
            }

            $content = substr($document->content, $previousByteOffset, $byteOffset - $previousByteOffset);
            $dictionary = DictionaryParser::parse($content, $document->errorCollection);
            $decodedStream = ObjectStreamContentParser::parse($content, $dictionary);
            $objectStreams[] = new ObjectStream(
                (int) $objectIndicators[0],
                (int) $objectIndicators[1],
                $content,
                $decodedStream,
                ObjectParser::parse($decodedStream, $document->errorCollection),
                $dictionary,
            );
            $previousByteOffset = $byteOffset;
        }

        return new ObjectStreamCollection(...$objectStreams);
    }
}
