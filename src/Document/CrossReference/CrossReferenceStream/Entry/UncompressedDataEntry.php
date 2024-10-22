<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceStream\Entry;

class UncompressedDataEntry {
    public function __construct(
        public readonly int $objNumberOrByteOffset,
        public readonly int $generationNumber
    ) {
    }
}
