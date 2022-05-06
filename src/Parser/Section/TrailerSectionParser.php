<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Parser\Section;

use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Document\Trailer;
use PrinsFrank\PdfParser\Enum\Marker;
use PrinsFrank\PdfParser\Exception\MarkerNotFoundException;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

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
            throw new ParseFailureException('Failed to retrieve the byte offset for the last cross reference section. Document lenght: "' . $document->fileLength . '", eof marker pos: "' . $eofMarkerPos . '"');
        }
        $trailer->setByteOffsetLastCrossReferenceSection((int) $byteOffsetLastCrossReferenceSection);

        $document->setTrailer($trailer);
    }
}
