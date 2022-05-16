<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceData;

class CrossReferenceData
{
    public function __construct(
        public readonly int|string $type,
        public readonly int|string $objNumberOrByteOffset,
        public readonly int|string $generationNumber
    ) { }
}
