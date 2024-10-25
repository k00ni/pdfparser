<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Version;

use PrinsFrank\PdfParser\Document\Generic\Marker;
use PrinsFrank\PdfParser\Exception\UnsupportedFileFormatException;
use PrinsFrank\PdfParser\Exception\UnsupportedPdfVersionException;
use PrinsFrank\PdfParser\Pdf;

class VersionParser {
    /**
     * @throws UnsupportedFileFormatException
     * @throws UnsupportedPdfVersionException
     */
    public static function parse(Pdf $pdf): Version {
        if ($pdf->read(0, Marker::VERSION->length()) !== Marker::VERSION->value) {
            throw new UnsupportedFileFormatException();
        }

        $versionString = $pdf->read(strlen(Marker::VERSION->value), Version::length());
        $version = Version::tryFrom($versionString);
        if ($version === null) {
            throw new UnsupportedPdfVersionException($versionString);
        }

        return $version;
    }
}
