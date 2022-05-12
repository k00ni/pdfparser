<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Parser\Section;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceSource;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceStream\CrossReferenceStream;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable\CrossReferenceTable;
use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Enum\Marker;
use PrinsFrank\PdfParser\Filter\Decode\FlateDecode;
use PrinsFrank\PdfParser\Parser\Dictionary\DictionaryParser;

class CrossReferenceSourceParser
{
    public static function parse(Document $document): CrossReferenceSource
    {
        return static::parseStream($document);
    }

    public static function parseTable(Document $document): CrossReferenceTable
    {

    }

    public static function parseStream(Document $document): CrossReferenceStream
    {
        $content = substr($document->content, $document->trailer->byteOffsetLastCrossReferenceSection, $document->trailer->startXrefMarkerPos - $document->trailer->byteOffsetLastCrossReferenceSection);
        $dictionary = DictionaryParser::parse($content);

        return new CrossReferenceTable();
    }
}
