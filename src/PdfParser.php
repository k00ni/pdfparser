<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceSourceParser;
use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Document\Version\VersionParser;
use PrinsFrank\PdfParser\Exception\PdfParserException;

final class PdfParser {
    /** @throws PdfParserException */
    public function parse(Stream $stream): Document {
        return new Document(
            $stream,
            VersionParser::parse($stream),
            CrossReferenceSourceParser::parse($stream),
        );
    }

    public function parseFile(string $filePath): Document {
        return $this->parse(
            Stream::openFile($filePath)
        );
    }

    public function parseString(string $content): Document {
        return $this->parse(
            Stream::fromString($content)
        );
    }
}
