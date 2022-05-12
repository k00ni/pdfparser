<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser;

use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Exception\PdfParserException;
use PrinsFrank\PdfParser\Parser\Section\CrossReferenceTableParser;
use PrinsFrank\PdfParser\Parser\Section\VersionParser;
use PrinsFrank\PdfParser\Parser\Section\TrailerSectionParser;

final class PdfParser
{
    /**
     * @throws PdfParserException
     */
    public function parse(string $fileContent): Document
    {
        $document = new Document($fileContent);

        return $document->setVersion(VersionParser::parse($document))
            ->setTrailer(TrailerSectionParser::parse($document))
            ->setCrossReferenceTable(CrossReferenceTableParser::parse($document));
    }
}
