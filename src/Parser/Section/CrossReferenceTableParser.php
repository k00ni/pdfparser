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
        var_dump($dictionary);exit;

        $startStream = strpos($content, 'stream');
        $endStream = strpos($content, 'endstream');
        $stream = substr($content, $startStream + strlen('stream'), $endStream - $startStream - strlen('stream'));

        echo($content) . PHP_EOL;
        echo(123). PHP_EOL;
        echo($stream). PHP_EOL;
        echo(123). PHP_EOL;
        echo(bin2hex(gzuncompress(trim($stream)))). PHP_EOL;
        exit;

        return new CrossReferenceTable();
    }
}
