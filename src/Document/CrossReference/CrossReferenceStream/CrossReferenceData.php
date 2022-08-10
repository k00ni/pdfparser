<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceData;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceStream\CrossReferenceStreamType;

class CrossReferenceData
{
    public readonly CrossReferenceStreamType $type;

    public function __construct(
        int|string $type,
        public readonly int|string $objNumberOrByteOffset,
        public readonly int|string $generationNumber
    ) {
        $this->type = CrossReferenceStreamType::from($type);
    }
}
