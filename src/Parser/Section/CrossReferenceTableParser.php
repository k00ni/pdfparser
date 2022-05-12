<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Parser\Section;

use PrinsFrank\PdfParser\Document\CrossReferenceTable\CrossReferenceTable;
use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Parser\KeyValuePairParser;

class CrossReferenceTableParser
{
    public static function parse(Document $document): CrossReferenceTable
    {
        $trailer = $document->trailer;
        $content = substr($document->content, $trailer->byteOffsetLastCrossReferenceSection, $trailer->startXrefMarkerPos - $trailer->byteOffsetLastCrossReferenceSection);
        $dictionary = KeyValuePairParser::parse($content);

        return new CrossReferenceTable();
    }
}
