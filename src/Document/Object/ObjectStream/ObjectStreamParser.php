<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\ObjectStream;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParser;
use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Document\Errors\Error;
use PrinsFrank\PdfParser\Document\Generic\Character\WhitespaceCharacter;
use PrinsFrank\PdfParser\Document\Object\ObjectParser;
use PrinsFrank\PdfParser\Document\Object\ObjectStream\ObjectStreamContent\ObjectStreamContentParser;
use PrinsFrank\PdfParser\Exception\BufferTooSmallException;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

class ObjectStreamParser {
    /** @throws ParseFailureException|BufferTooSmallException */
    public static function parse(Document $document): ObjectStreamCollection {
        $byteOffsets = array_unique($document->crossReferenceSource->getByteOffsets());
        if (count($byteOffsets) === 1) {
            $document->errorCollection->addError(new Error('Only 1 byte offset was retrieved.'));
        }

        $objectStreams = [];
        sort($byteOffsets);
        foreach ($byteOffsets as $key => $byteOffset) {
            if ($byteOffset === 0) {
                continue; // Contains PDF header and binary data to indicate document contains binary data
            }

            $firstNewLinePos = self::getFirstNewLinePos($document, $byteOffset);
            $firstNewLinePos = ($firstNewLinePos === $byteOffset ? self::getFirstNewLinePos($document, $byteOffset + 1) : $firstNewLinePos);
            $firstLine = $document->stream->read($byteOffset, $firstNewLinePos - $byteOffset);
            $objectIndicators = explode(' ', $firstLine);
            if (count($objectIndicators) !== 3 || $objectIndicators[2] !== 'obj') {
                throw new ParseFailureException(sprintf('Expected an object identifier in format (\d \d obj), got "%s" with offset %d and first new line at %d', $firstLine, $byteOffset, $firstNewLinePos));
            }

            $content = $document->stream->read($byteOffset, $byteOffsets[$key + 1] ?? $document->stream->getSizeInBytes());
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
        }

        return new ObjectStreamCollection(...$objectStreams);
    }

    /**
     * @param Document $document
     * @param int $byteOffset
     * @return mixed
     */
    public static function getFirstNewLinePos(Document $document, int $byteOffset): mixed
    {
        $firstLineFeed = $document->stream->strpos(WhitespaceCharacter::LINE_FEED->value, $byteOffset);
        $firstCarriageReturn = $document->stream->strpos(WhitespaceCharacter::CARRIAGE_RETURN->value, $byteOffset);
        if ($firstLineFeed === null && $firstCarriageReturn === null) {
            throw new ParseFailureException('Expected a line field ...');
        }

        if ($firstCarriageReturn === null) {
            return $firstLineFeed;
        }

        if ($firstLineFeed === null) {
            return $firstCarriageReturn;
        }

        return min($firstLineFeed, $firstCarriageReturn);
    }
}
