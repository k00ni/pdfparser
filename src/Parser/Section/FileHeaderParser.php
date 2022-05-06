<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Parser\Section;

use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Enum\Marker;
use PrinsFrank\PdfParser\Enum\Version;
use PrinsFrank\PdfParser\Exception\UnsupportedFileFormatException;
use PrinsFrank\PdfParser\Exception\UnsupportedPdfVersionException;

class FileHeaderParser implements SectionParser
{
    /**
     * @throws UnsupportedFileFormatException
     * @throws UnsupportedPdfVersionException
     */
    public static function parse(Document $document): void
    {
        if (str_starts_with($document->content, Marker::VERSION->value) === false) {
            throw new UnsupportedFileFormatException();
        }

        $versionString = mb_substr($document->content, mb_strlen(Marker::VERSION->value), Version::length());
        $version = Version::tryFrom($versionString);
        if ($version === null) {
            throw new UnsupportedPdfVersionException($versionString);
        }

        $document->setVersion($version);
    }
}
