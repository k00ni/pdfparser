<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Parser\Section;

use PrinsFrank\PdfParser\Document\Trailer\Trailer;

class FileTrailerDictionaryParser
{
    public static function parse(Trailer $trailer): void
    {
        $content = substr($trailer->document->content, $trailer->byteOffsetLastCrossReferenceSection, $trailer->startXrefMarkerPos - $trailer->byteOffsetLastCrossReferenceSection);
        echo 'Foo';
        print_r($content);
        echo 'bar';
        exit;
    }
}
