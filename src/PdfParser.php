<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceSourceParser;
use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Document\Object\ObjectStream\ObjectStreamParser;
use PrinsFrank\PdfParser\Document\Trailer\TrailerSectionParser;
use PrinsFrank\PdfParser\Document\Version\VersionParser;
use PrinsFrank\PdfParser\Exception\PdfParserException;

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
            ->setCrossReferenceSource(CrossReferenceSourceParser::parse($document))
//            ->setObjectStreams(...ObjectStreamParser::parse($document))
        ;
    }
}
