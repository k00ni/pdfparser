<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Unused\Object\ObjectStream;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceSource;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParser;
use PrinsFrank\PdfParser\Document\Errors\Error;
use PrinsFrank\PdfParser\Document\Errors\ErrorCollection;
use PrinsFrank\PdfParser\Document\Generic\Marker;
use PrinsFrank\PdfParser\Exception\BufferTooSmallException;
use PrinsFrank\PdfParser\Exception\ParseFailureException;
use PrinsFrank\PdfParser\Stream;

class ObjectStreamParser {
    /** @throws ParseFailureException|BufferTooSmallException */
    public static function parse(Stream $stream, CrossReferenceSource $crossReferenceSource, ErrorCollection $errorCollection): ObjectStreamCollection {
        $byteOffsets = array_unique($crossReferenceSource->getByteOffsets());
        if (count($byteOffsets) === 1) {
            $errorCollection->addError(new Error('Only 1 byte offset was retrieved.'));
        }

        $objectStreams = [];
        sort($byteOffsets);
        foreach ($byteOffsets as $key => $byteOffset) {
            if ($byteOffset === 0) {
                continue; // Contains PDF header and binary data to indicate document contains binary data
            }

            $firstNewLinePos = $stream->getEndOfCurrentLine($byteOffset, $byteOffsets[$key + 1] ?? $stream->getSizeInBytes());
            $firstNewLinePos = ($firstNewLinePos === $byteOffset ? $stream->getEndOfCurrentLine($byteOffset + 1, $byteOffsets[$key + 1] ?? $stream->getSizeInBytes()) : $firstNewLinePos);
            $firstLine = $stream->read($byteOffset, $firstNewLinePos - $byteOffset);
            $objectIndicators = explode(' ', $firstLine);
            if (count($objectIndicators) !== 3 || $objectIndicators[2] !== Marker::OBJ->value) {
                throw new ParseFailureException(sprintf('Expected an object identifier in format (\d \d obj), got "%s" with offset %d and first new line at %d', $firstLine, $byteOffset, $firstNewLinePos));
            }

            $dictionary = DictionaryParser::parse($stream, $byteOffset, ($byteOffsets[$key + 1] ?? $stream->getSizeInBytes()) - $byteOffset, $errorCollection);
            $objectStreams[] = new ObjectStream(
                (int) $objectIndicators[0],
                (int) $objectIndicators[1],
                $dictionary,
            );
        }

        return new ObjectStreamCollection(...$objectStreams);
    }
}
