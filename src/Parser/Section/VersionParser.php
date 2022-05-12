<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Parser\Section;

use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Enum\Marker;
use PrinsFrank\PdfParser\Enum\Version;
use PrinsFrank\PdfParser\Exception\UnsupportedFileFormatException;
use PrinsFrank\PdfParser\Exception\UnsupportedPdfVersionException;

class VersionParser
{
    /**
     * @throws UnsupportedFileFormatException
     * @throws UnsupportedPdfVersionException
     */
    public static function parse(Document $document): Version
    {
        if (str_starts_with($document->content, Marker::VERSION->value) === false) {
            throw new UnsupportedFileFormatException();
        }

        $versionString = substr($document->content, strlen(Marker::VERSION->value), Version::length());
        $version = Version::tryFrom($versionString);
        if ($version === null) {
            throw new UnsupportedPdfVersionException($versionString);
        }

        return $version;
    }
}
