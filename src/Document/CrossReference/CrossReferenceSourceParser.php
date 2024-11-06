<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference;

use PrinsFrank\PdfParser\Document\CrossReference\Source\CrossReferenceSource;
use PrinsFrank\PdfParser\Document\CrossReference\Stream\CrossReferenceStreamParser;
use PrinsFrank\PdfParser\Document\CrossReference\Table\CrossReferenceTableParser;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Integer\IntegerValue;
use PrinsFrank\PdfParser\Document\Generic\Marker;
use PrinsFrank\PdfParser\Exception\MarkerNotFoundException;
use PrinsFrank\PdfParser\Exception\ParseFailureException;
use PrinsFrank\PdfParser\Stream;

class CrossReferenceSourceParser {
    /** @throws ParseFailureException */
    public static function parse(Stream $stream): CrossReferenceSource {
        $eofMarkerPos = $stream->strrpos(Marker::EOF->value, 0)
            ?? throw new MarkerNotFoundException(Marker::EOF->value);
        $startXrefMarkerPos = $stream->strrpos(Marker::START_XREF->value, $stream->getSizeInBytes() - $eofMarkerPos)
            ?? throw new MarkerNotFoundException(Marker::START_XREF->value);
        $startByteOffset = $stream->getStartOfNextLine($startXrefMarkerPos, $stream->getSizeInBytes())
            ?? throw new ParseFailureException('Expected a carriage return or line feed after startxref marker, none found');
        $endByteOffset = $stream->getEndOfCurrentLine($startByteOffset, $stream->getSizeInBytes())
            ?? throw new ParseFailureException('Expected a carriage return or line feed after the byte offset, none found');

        $byteOffsetLastCrossReferenceSection = $stream->read($startByteOffset, $endByteOffset - $startByteOffset);
        if ($byteOffsetLastCrossReferenceSection !== (string)(int) $byteOffsetLastCrossReferenceSection) {
            throw new ParseFailureException(sprintf('Invalid byte offset last crossReference section "%s", "%s"', $byteOffsetLastCrossReferenceSection, $stream->read($startXrefMarkerPos, $stream->getSizeInBytes() - $startXrefMarkerPos)));
        }

        $byteOffsetLastCrossReferenceSection = (int) $byteOffsetLastCrossReferenceSection;
        if ($byteOffsetLastCrossReferenceSection > $stream->getSizeInBytes()) {
            throw new ParseFailureException(sprintf('Invalid byte offset: position of last crossReference section %d is greater than total size of stream %d. Should this be %d?', (int) $byteOffsetLastCrossReferenceSection, $stream->getSizeInBytes(), $stream->strrpos(Marker::XREF->value, $stream->getSizeInBytes() - $startXrefMarkerPos) ?? $stream->strrpos(Marker::OBJ->value, $stream->getSizeInBytes() - $startXrefMarkerPos)));
        }

        $eolPosByteOffset = $stream->getEndOfCurrentLine($byteOffsetLastCrossReferenceSection, $stream->getSizeInBytes())
            ?? throw new ParseFailureException('Expected a newline after byte offset for last cross reference stream');

        $firstLineCrossReferenceSource = $stream->read($byteOffsetLastCrossReferenceSection, $eolPosByteOffset - $byteOffsetLastCrossReferenceSection);
        if ($firstLineCrossReferenceSource === Marker::XREF->value) {
            $currentCrossReferenceTable = CrossReferenceTableParser::parse($stream, $eolPosByteOffset, $startXrefMarkerPos - $eolPosByteOffset);
            $crossReferenceTables = [$currentCrossReferenceTable];
            while (($previous = $currentCrossReferenceTable->dictionary->getEntryWithKey(DictionaryKey::PREVIOUS)?->value) instanceof IntegerValue) {
                $eolPosByteOffset = $stream->getEndOfCurrentLine($previous->value, $stream->getSizeInBytes())
                    ?? throw new ParseFailureException('Expected a newline after byte offset for last cross reference stream');
                $startXrefMarkerPos = $stream->strpos(Marker::START_XREF->value, $previous->value, $stream->getSizeInBytes() - $previous->value)
                    ?? throw new ParseFailureException('Unable to locate startxref');

                $currentCrossReferenceTable = CrossReferenceTableParser::parse($stream, $eolPosByteOffset, $startXrefMarkerPos - $eolPosByteOffset);
                $crossReferenceTables[] = $currentCrossReferenceTable;
            }

            return new CrossReferenceSource(... $crossReferenceTables);
        }

        $endCrossReferenceStream = $stream->strpos(Marker::END_OBJ->value, $byteOffsetLastCrossReferenceSection, $stream->getSizeInBytes());
        if ($endCrossReferenceStream === null) {
            throw new ParseFailureException('Unable to locate end of crossReferenceStream object');
        }

        // TODO: Multiple
        return CrossReferenceStreamParser::parse(
            $stream,
            $byteOffsetLastCrossReferenceSection,
            $endCrossReferenceStream - $byteOffsetLastCrossReferenceSection
        );
    }
}
