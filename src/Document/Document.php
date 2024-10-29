<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceSource;

use PrinsFrank\PdfParser\Document\Errors\ErrorCollection;
use PrinsFrank\PdfParser\Document\Object\ObjectItem;
use PrinsFrank\PdfParser\Document\Object\ObjectItemParser;
use PrinsFrank\PdfParser\Document\Trailer\Trailer;
use PrinsFrank\PdfParser\Document\Version\Version;
use PrinsFrank\PdfParser\Stream;

final class Document {
    public function __construct(
        public readonly Stream               $stream,
        public readonly Version              $version,
        public readonly Trailer              $trailer,
        public readonly CrossReferenceSource $crossReferenceSource,
        public readonly ErrorCollection      $errorCollection,
    ) {
    }

    public function getObject(int $objectNumber): ?ObjectItem {
        return ObjectItemParser::parseObject($objectNumber, $this->crossReferenceSource, $this->stream, $this->trailer, $this->errorCollection);
    }
}
