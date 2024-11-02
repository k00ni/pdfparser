<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceStream\CrossReferenceStreamParser;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable\CrossReferenceTableParser;
use PrinsFrank\PdfParser\Document\Generic\Marker;
use PrinsFrank\PdfParser\Document\Trailer\Trailer;
use PrinsFrank\PdfParser\Exception\ParseFailureException;
use PrinsFrank\PdfParser\Stream;

class CrossReferenceSourceParser {
    /** @throws ParseFailureException */
    public static function parse(Stream $stream, Trailer $trailer): CrossReferenceSource {
        $eolPosByteOffset = $stream->getEndOfCurrentLine($trailer->byteOffsetLastCrossReferenceSection, $stream->getSizeInBytes());
        if ($eolPosByteOffset === null) {
            throw new ParseFailureException('Expected a newline after byte offset for last cross reference stream');
        }

        $firstLineCrossReferenceSource = $stream->read($trailer->byteOffsetLastCrossReferenceSection, $eolPosByteOffset - $trailer->byteOffsetLastCrossReferenceSection);
        if ($firstLineCrossReferenceSource === Marker::XREF->value) {
            $nextTrailerPos = $stream->strpos(Marker::TRAILER->value, $trailer->byteOffsetLastCrossReferenceSection, $stream->getSizeInBytes());

            return CrossReferenceTableParser::parse($stream, $trailer->byteOffsetLastCrossReferenceSection, $nextTrailerPos - $trailer->byteOffsetLastCrossReferenceSection);
        }

        $endCrossReferenceStream = $stream->strpos(Marker::END_OBJ->value, $trailer->byteOffsetLastCrossReferenceSection, $stream->getSizeInBytes());
        if ($endCrossReferenceStream === null) {
            throw new ParseFailureException('Unable to locate end of crossReferenceStream object');
        }

        return CrossReferenceStreamParser::parse(
            $stream,
            $trailer->byteOffsetLastCrossReferenceSection,
            $endCrossReferenceStream - $trailer->byteOffsetLastCrossReferenceSection
        );
    }
}
