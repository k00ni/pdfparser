<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceStream\CrossReferenceStream;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable\CrossReferenceTable;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParser;
use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Document\Filter\Decode\FlateDecode;
use PrinsFrank\PdfParser\Document\Generic\Marker;

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
