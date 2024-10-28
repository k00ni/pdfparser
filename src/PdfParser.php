<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceSourceParser;
use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Document\Errors\ErrorCollection;
use PrinsFrank\PdfParser\Document\Object\ObjectStream\ObjectStreamParser;
use PrinsFrank\PdfParser\Document\Trailer\TrailerSectionParser;
use PrinsFrank\PdfParser\Document\Version\VersionParser;
use PrinsFrank\PdfParser\Exception\PdfParserException;

final class PdfParser {
    /** @throws PdfParserException */
    public function parse(Stream $stream): Document {
        $errorCollection = new ErrorCollection();

        $version = VersionParser::parse($stream);
        $trailer = TrailerSectionParser::parse($stream, $errorCollection);
        $crossReferenceSource = CrossReferenceSourceParser::parse($stream, $trailer, $errorCollection);
//        $objectStreamCollection = ObjectStreamParser::parse($stream, $crossReferenceSource, $errorCollection);

        return new Document(
            $stream,
            $version,
            $trailer,
            $crossReferenceSource,
            $errorCollection
        );
    }
}
