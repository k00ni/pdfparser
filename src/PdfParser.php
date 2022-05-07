<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser;

use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Exception\PdfParserException;
use PrinsFrank\PdfParser\Parser\Section\FileHeaderParser;
use PrinsFrank\PdfParser\Parser\Section\TrailerSectionParser;

final class PdfParser
{
    /**
     * @throws PdfParserException
     */
    public function parse(string $fileContent): Document
    {
        $document = new Document($fileContent);

        FileHeaderParser::parse($document);
        TrailerSectionParser::parse($document);

        return $document;
    }
}
