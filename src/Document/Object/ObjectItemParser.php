<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable\CrossReferenceTable;
use PrinsFrank\PdfParser\Document\Generic\Character\WhitespaceCharacter;
use PrinsFrank\PdfParser\Document\Generic\Marker;
use PrinsFrank\PdfParser\Document\Trailer\Trailer;
use PrinsFrank\PdfParser\Exception\ParseFailureException;
use PrinsFrank\PdfParser\Stream;

class ObjectItemParser {
    public static function parseObject(int $objectNumber, CrossReferenceTable $crossReferenceSource, Stream $stream, Trailer $trailer): ObjectItem {
        $crossReferenceEntry = $crossReferenceSource->getCrossReferenceEntry($objectNumber);
        if ($crossReferenceEntry === null) {
            throw new ParseFailureException();
        }

        $nextByteOffset = $crossReferenceSource->getNextByteOffset($crossReferenceEntry->byteOffsetInDecodedStream) ?? $trailer->startTrailerMarkerPos;
        $firstLine = $stream->read($crossReferenceEntry->byteOffsetInDecodedStream, $stream->getEndOfCurrentLine($crossReferenceEntry->byteOffsetInDecodedStream, $nextByteOffset) - $crossReferenceEntry->byteOffsetInDecodedStream);
        $objHeaderParts = explode(WhitespaceCharacter::SPACE->value, $firstLine);
        if (count($objHeaderParts) !== 3 || (int) $objHeaderParts[0] !== $objectNumber || (int) $objHeaderParts[1] !== $crossReferenceEntry->generationNumber || $objHeaderParts[2] !== Marker::OBJ->value) {
            throw new ParseFailureException(sprintf('Expected %d %d %s on first line, got "%s"', $objectNumber, $crossReferenceEntry->generationNumber, Marker::OBJ->value, $firstLine));
        }

        return new ObjectItem(
            $objectNumber,
            $crossReferenceEntry->generationNumber,
            $crossReferenceEntry->byteOffsetInDecodedStream,
            $nextByteOffset,
        );
    }
}
