<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceSourceParser;
use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Document\Trailer\TrailerSectionParser;
use PrinsFrank\PdfParser\Document\Version\VersionParser;
use PrinsFrank\PdfParser\Exception\PdfParserException;

final class PdfParser {
    /** @throws PdfParserException */
    public function parse(Stream $stream): Document {
        $version = VersionParser::parse($stream);
        $trailer = TrailerSectionParser::parse($stream);
        $crossReferenceSource = CrossReferenceSourceParser::parse($stream, $trailer);

        return new Document(
            $stream,
            $version,
            $crossReferenceSource,
            $trailer,
        );
    }
}
