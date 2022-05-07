<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Parser\Section;

use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Document\Trailer;
use PrinsFrank\PdfParser\Enum\Marker;
use PrinsFrank\PdfParser\Exception\MarkerNotFoundException;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

/**
 * PDF 32000-1:2008
 * Conforming readers should read a PDF file from its end. The last line of the file shall contain only the end-of-file
 * marker, %%EOF. The two preceding lines shall contain, one per line and in order, the keyword startxref and the byte
 * offset in the decoded stream from the beginning of the file to the beginning of the xref keyword in the last
 * cross-reference section. The startxref line shall be preceded by the trailer dictionary, consisting of the keyword
 * trailer followed by a series of key-value pairs enclosed in double angle brackets (<<...>>) (using LESS-THAN SIGNs
 * (3ch) and GREATER-THAN SIGNs (3Eh)).
 */
class TrailerSectionParser implements SectionParser
{
    /**
     * @throws MarkerNotFoundException
     * @throws ParseFailureException
     */
    public static function parse(Document $document): void
    {
        $trailer = new Trailer($document);

        $eofMarkerPos = strrpos($document->content, Marker::EOF->value);
        if ($eofMarkerPos === false) {
            throw new MarkerNotFoundException(Marker::EOF->value);
        }
        $trailer->setEofMarkerPos($eofMarkerPos);

        $startXrefMarkerPos = strrpos($document->content, Marker::START_XREF->value, -($document->fileLength - $eofMarkerPos));
        if ($startXrefMarkerPos === false) {
            throw new MarkerNotFoundException(Marker::START_XREF->value);
        }
        $trailer->setStartXrefMarkerPos($startXrefMarkerPos);

        $byteOffsetLastCrossReferenceSection = substr($document->content, $startXrefMarkerPos + strlen(Marker::START_XREF->value),  -($document->fileLength - $eofMarkerPos));
        if ($byteOffsetLastCrossReferenceSection === false) {
            throw new ParseFailureException('Failed to retrieve the byte offset for the last cross reference section. Document length: "' . $document->fileLength . '", eof marker pos: "' . $eofMarkerPos . '"');
        }
        $trailer->setByteOffsetLastCrossReferenceSection((int) $byteOffsetLastCrossReferenceSection);

        $document->setTrailer($trailer);
    }
}
