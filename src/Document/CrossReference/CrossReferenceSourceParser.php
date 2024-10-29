<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceStream\CrossReferenceStreamParser;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable\CrossReferenceTableParser;
use PrinsFrank\PdfParser\Document\Errors\ErrorCollection;
use PrinsFrank\PdfParser\Document\Generic\Marker;
use PrinsFrank\PdfParser\Document\Trailer\Trailer;
use PrinsFrank\PdfParser\Exception\ParseFailureException;
use PrinsFrank\PdfParser\Stream;

class CrossReferenceSourceParser {
    /** @throws ParseFailureException */
    public static function parse(Stream $stream, Trailer $trailer, ErrorCollection $errorCollection): CrossReferenceSource {
        $eolPosByteOffset = $stream->getEndOfCurrentLine($trailer->byteOffsetLastCrossReferenceSection, $stream->getSizeInBytes());
        if ($eolPosByteOffset === null) {
            throw new ParseFailureException('Expected a newline after byte offset for last cross reference stream');
        }

        $firstLineCrossReferenceSource = $stream->read($trailer->byteOffsetLastCrossReferenceSection, $eolPosByteOffset - $trailer->byteOffsetLastCrossReferenceSection);
        if ($firstLineCrossReferenceSource === Marker::XREF->value) {
            return CrossReferenceTableParser::parse($stream, $trailer->byteOffsetLastCrossReferenceSection, $trailer->startTrailerMarkerPos - $trailer->byteOffsetLastCrossReferenceSection);
        }

        var_dump($trailer);
        exit;
        return CrossReferenceStreamParser::parse();
    }
}
