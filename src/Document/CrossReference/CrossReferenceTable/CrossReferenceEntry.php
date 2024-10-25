<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable;

class CrossReferenceEntry {
    public function __construct(
        public readonly int $offset,
        public readonly int $generationNumber,
        public readonly ObjectInUseOrFreeCharacter $inUseOrFreeCharacter
    ) {
    }
}
