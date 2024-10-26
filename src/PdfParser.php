<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceSourceParser;
use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Document\Errors\ErrorCollection;
use PrinsFrank\PdfParser\Document\Trailer\TrailerSectionParser;
use PrinsFrank\PdfParser\Document\Version\VersionParser;
use PrinsFrank\PdfParser\Exception\PdfParserException;

final class PdfParser {
    /** @throws PdfParserException */
    public function parse(Pdf $pdf): Document {
        $errorCollection = new ErrorCollection();

        $version = VersionParser::parse($pdf);
        $trailer = TrailerSectionParser::parse($pdf, $errorCollection);
//        $crossReferenceSource = CrossReferenceSourceParser::parse($pdf, $trailer, $errorCollection);

        return new Document($pdf, $version, $trailer, $errorCollection);
    }
}
