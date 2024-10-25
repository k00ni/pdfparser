<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Trailer;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParser;
use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Document\Errors\ErrorCollection;
use PrinsFrank\PdfParser\Document\Generic\Marker;
use PrinsFrank\PdfParser\Exception\MarkerNotFoundException;
use PrinsFrank\PdfParser\Exception\ParseFailureException;
use PrinsFrank\PdfParser\Pdf;

/**
 * PDF 32000-1:2008
 * Conforming readers should read a PDF file from its end. The last line of the file shall contain only the end-of-file
 * marker, %%EOF. The two preceding lines shall contain, one per line and in order, the keyword startxref and the byte
 * offset in the decoded stream from the beginning of the file to the beginning of the xref keyword in the last
 * cross-reference section. The startxref line shall be preceded by the trailer dictionary, consisting of the keyword
 * trailer followed by a series of key-value pairs enclosed in double angle brackets (<<...>>) (using LESS-THAN SIGNs
 * (3ch) and GREATER-THAN SIGNs (3Eh)).
 */
class TrailerSectionParser {
    /**
     * @throws MarkerNotFoundException
     * @throws ParseFailureException
     */
    public static function parse(Pdf $pdf, ErrorCollection $errorCollection): Trailer {
        $trailer = new Trailer();

        $eofMarkerPos = $pdf->strrpos(Marker::EOF->value, 0);
        if ($eofMarkerPos === null) {
            throw new MarkerNotFoundException(Marker::EOF->value);
        }
        $trailer->setEofMarkerPos($eofMarkerPos);

        $startXrefMarkerPos = $pdf->strrpos(Marker::START_XREF->value, $pdf->getSizeInBytes() - $eofMarkerPos);
        if ($startXrefMarkerPos === null) {
            throw new MarkerNotFoundException(Marker::START_XREF->value);
        }
        $trailer->setStartXrefMarkerPos($startXrefMarkerPos);

        $byteOffsetLastCrossReferenceSection = $pdf->read($startXrefMarkerPos + strlen(Marker::START_XREF->value), $pdf->getSizeInBytes() - $eofMarkerPos);
        if ($byteOffsetLastCrossReferenceSection === null) {
            throw new ParseFailureException('Failed to retrieve the byte offset for the last cross reference section. Document length: "' . $pdf->getSizeInBytes() . '", eof marker pos: "' . $eofMarkerPos . '"');
        }
        $trailer->setByteOffsetLastCrossReferenceSection((int) $byteOffsetLastCrossReferenceSection);

        $trailerMarkerPos = $pdf->strrpos(Marker::TRAILER->value, $pdf->getSizeInBytes() - $startXrefMarkerPos);
        if ($trailerMarkerPos === null) {
            $trailer->setStartTrailerMarkerPos(null);
            $trailer->setDictionary(null);
        } else {
            $trailer->setStartTrailerMarkerPos($trailerMarkerPos);
            $trailer->setDictionary(DictionaryParser::parse($pdf->read($trailer->startTrailerMarkerPos, $trailer->startXrefMarkerPos - $trailer->startTrailerMarkerPos), $errorCollection));
        }

        return $trailer;
    }
}
