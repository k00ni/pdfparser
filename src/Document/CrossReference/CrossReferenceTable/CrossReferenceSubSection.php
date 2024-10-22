<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable;

class CrossReferenceSubSection {
    /** @var array<CrossReferenceEntry> */
    public array $crossReferenceEntries = [];

    public function __construct(
        public readonly int $firstObjectNumber,
        public readonly int $nrOfEntries
    ) {
    }

    public function addCrossReferenceEntry(CrossReferenceEntry $crossReferenceEntry): self {
        $this->crossReferenceEntries[] = $crossReferenceEntry;

        return $this;
    }
}
