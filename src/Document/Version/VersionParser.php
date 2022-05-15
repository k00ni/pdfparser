<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Version;

use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Document\Generic\Marker;
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
