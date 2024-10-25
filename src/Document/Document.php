<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceSource;

use PrinsFrank\PdfParser\Document\Errors\ErrorCollection;
use PrinsFrank\PdfParser\Document\Trailer\Trailer;
use PrinsFrank\PdfParser\Document\Version\Version;
use PrinsFrank\PdfParser\Pdf;

final class Document {
    public function __construct(
        public readonly Pdf                  $pdf,
        public readonly Version              $version,
        public readonly Trailer              $trailer,
        public readonly CrossReferenceSource $crossReferenceSource,
        public readonly ErrorCollection      $errorCollection,
    ){
    }
}
