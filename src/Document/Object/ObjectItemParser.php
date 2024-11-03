<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object;

use PrinsFrank\PdfParser\Document\CrossReference\Source\CrossReferenceSource;
use PrinsFrank\PdfParser\Document\Generic\Character\WhitespaceCharacter;
use PrinsFrank\PdfParser\Document\Generic\Marker;
use PrinsFrank\PdfParser\Document\Trailer\Trailer;
use PrinsFrank\PdfParser\Exception\ParseFailureException;
use PrinsFrank\PdfParser\Stream;

class ObjectItemParser {
    public static function parseObject(int $objectNumber, CrossReferenceSource $crossReferenceSource, Stream $stream, Trailer $trailer): ObjectItem {
        $crossReferenceEntry = $crossReferenceSource->getCrossReferenceEntry($objectNumber);
        if ($crossReferenceEntry === null) {
            throw new ParseFailureException(sprintf('No crossReference entry found for object with number %d', $objectNumber));
        }

        $nextByteOffset = $crossReferenceSource->getNextByteOffset($crossReferenceEntry->byteOffsetInDecodedStream) ?? $trailer->startTrailerMarkerPos;
        $objHeader = $stream->read($crossReferenceEntry->byteOffsetInDecodedStream, $stream->strpos(Marker::OBJ->value, $crossReferenceEntry->byteOffsetInDecodedStream, $nextByteOffset) + strlen(Marker::OBJ->value) - $crossReferenceEntry->byteOffsetInDecodedStream);
        $objHeaderParts = explode(WhitespaceCharacter::SPACE->value, str_replace([WhitespaceCharacter::LINE_FEED->value], ' ', trim($objHeader)));
        if (count($objHeaderParts) !== 3 || (int) $objHeaderParts[0] !== $objectNumber || (int) $objHeaderParts[1] !== $crossReferenceEntry->generationNumber || $objHeaderParts[2] !== Marker::OBJ->value) {
            throw new ParseFailureException(sprintf('Expected "%d %d %s" on first line, got "%s"', $objectNumber, $crossReferenceEntry->generationNumber, Marker::OBJ->value, $objHeader));
        }

        return new ObjectItem(
            $objectNumber,
            $crossReferenceEntry->generationNumber,
            $crossReferenceEntry->byteOffsetInDecodedStream,
            $nextByteOffset,
        );
    }
}
