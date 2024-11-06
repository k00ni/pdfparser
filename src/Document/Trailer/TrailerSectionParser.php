<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Trailer;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParser;
use PrinsFrank\PdfParser\Document\Generic\Marker;
use PrinsFrank\PdfParser\Exception\MarkerNotFoundException;
use PrinsFrank\PdfParser\Exception\ParseFailureException;
use PrinsFrank\PdfParser\Stream;

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
    public static function parse(Stream $stream): Trailer {
        $eofMarkerPos = $stream->strrpos(Marker::EOF->value, 0);
        if ($eofMarkerPos === null) {
            throw new MarkerNotFoundException(Marker::EOF->value);
        }

        $startXrefMarkerPos = $stream->strrpos(Marker::START_XREF->value, $stream->getSizeInBytes() - $eofMarkerPos);
        if ($startXrefMarkerPos === null) {
            throw new MarkerNotFoundException(Marker::START_XREF->value);
        }

        $startByteOffset = $stream->getStartOfNextLine($startXrefMarkerPos, $stream->getSizeInBytes());
        if ($startByteOffset === null) {
            throw new ParseFailureException('Expected a carriage return or line feed after startxref marker, none found');
        }

        $endByteOffset = $stream->getEndOfCurrentLine($startByteOffset, $stream->getSizeInBytes());
        if ($endByteOffset === null) {
            throw new ParseFailureException('Expected a carriage return or line feed after the byte offset, none found');
        }

        $byteOffsetLastCrossReferenceSection = $stream->read($startByteOffset, $endByteOffset - $startByteOffset);
        if ($byteOffsetLastCrossReferenceSection !== (string)(int) $byteOffsetLastCrossReferenceSection) {
            throw new ParseFailureException(sprintf('Invalid byte offset last crossReference section "%s", "%s"', $byteOffsetLastCrossReferenceSection, $stream->read($startXrefMarkerPos, $stream->getSizeInBytes() - $startXrefMarkerPos)));
        }

        if ($byteOffsetLastCrossReferenceSection > $stream->getSizeInBytes()) {
            throw new ParseFailureException(sprintf('Invalid byte offset: position of last crossReference section %d is greater than total size of stream %d. Should this be %d?', (int) $byteOffsetLastCrossReferenceSection, $stream->getSizeInBytes(), $stream->strrpos(Marker::XREF->value, $stream->getSizeInBytes() - $startXrefMarkerPos) ?? $stream->strrpos(Marker::OBJ->value, $stream->getSizeInBytes() - $startXrefMarkerPos)));
        }

        $trailerMarkerPos = $stream->strrpos(Marker::TRAILER->value, $stream->getSizeInBytes() - $startXrefMarkerPos);
        return new Trailer(
            $eofMarkerPos,
            $startXrefMarkerPos,
            (int) $byteOffsetLastCrossReferenceSection,
            $trailerMarkerPos,
            $trailerMarkerPos === null
                ? null
                : DictionaryParser::parse($stream, $trailerMarkerPos, $startXrefMarkerPos - $trailerMarkerPos),
        );
    }
}
