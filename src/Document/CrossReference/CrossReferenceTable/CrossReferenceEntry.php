<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable;

use PrinsFrank\PdfParser\Document\Generic\Character\ObjectInUseOrFreeCharacter;

class CrossReferenceEntry {
    public readonly int                        $offset;
    public readonly int                        $generationNumber;
    public readonly ObjectInUseOrFreeCharacter $inUseOrFreeCharacter;

    public function __construct(int $offset, int $generationNumber, ObjectInUseOrFreeCharacter $inUseOrFreeCharacter) {
        $this->offset = $offset;
        $this->generationNumber = $generationNumber;
        $this->inUseOrFreeCharacter = $inUseOrFreeCharacter;
    }
}
