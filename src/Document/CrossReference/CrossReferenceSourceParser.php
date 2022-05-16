<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceData\CrossReferenceData;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceStream\CrossReferenceStream;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable\CrossReferenceTable;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParser;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\FilterNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Document\Generic\Marker;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

class CrossReferenceSourceParser
{
    public static function parse(Document $document): CrossReferenceSource
    {
        return static::parseStream($document);
    }

    public static function parseTable(Document $document): CrossReferenceTable
    {

    }

    /**
     * @throws ParseFailureException
     */
    public static function parseStream(Document $document): CrossReferenceStream
    {
        $content = substr($document->content, $document->trailer->byteOffsetLastCrossReferenceSection, $document->trailer->startXrefMarkerPos - $document->trailer->byteOffsetLastCrossReferenceSection);
        $dictionary = DictionaryParser::parse($content);

        $dictionaryType = $dictionary->getEntryWithKey(DictionaryKey::TYPE)?->value;
        if ($dictionaryType !== TypeNameValue::X_REF) {
            throw new ParseFailureException('Expected stream of type xref, got "' . ($dictionaryType?->name ?? 'Null') . '"');
        }

        $startStream = strpos($content, Marker::START_STREAM->value);
        $endStream = strpos($content, Marker::END_STREAM->value);
        $stream = substr($content, $startStream + strlen(Marker::START_STREAM->value), $endStream - $startStream - strlen(Marker::START_STREAM->value));

        $streamFilter = $dictionary->getEntryWithKey(DictionaryKey::FILTER)?->value;
        if ($streamFilter instanceof FilterNameValue) {
            $stream = $streamFilter::decode($streamFilter, $stream);
        }

        $wValue = $dictionary->getEntryWithKey(DictionaryKey::W)?->value?->value;
        if ($wValue === null) {
            throw new ParseFailureException('Missing W value, can\'t decode stream.');
        }

        $byteLengthRecord1 = ($wValue[0] ?? 0) * 2;
        $byteLengthRecord2 = ($wValue[1] ?? 0) * 2;
        $byteLengthRecord3 = ($wValue[2] ?? 0) * 2;
        $crossReferenceStream = new CrossReferenceStream();
        foreach (str_split($stream, $byteLengthRecord1 + $byteLengthRecord2 + $byteLengthRecord3) as $referenceRow) {
            $crossReferenceStream->addData(
                new CrossReferenceData(
                    substr($referenceRow, 0, $byteLengthRecord1),
                    substr($referenceRow, $byteLengthRecord1, $byteLengthRecord2),
                    substr($referenceRow, $byteLengthRecord2 + $byteLengthRecord1, $byteLengthRecord3),
                )
            );
        }

        return $crossReferenceStream;
    }
}
