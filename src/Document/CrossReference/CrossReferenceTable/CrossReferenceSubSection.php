<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable;

class CrossReferenceSubSection
{
    public readonly int $firstObjectNumber;
    public readonly int $nrOfEntries;
    public array $crossReferenceEntries = [];

    public function __construct(int $firstObjectNumber, int $nrOfEntries)
    {
        $this->firstObjectNumber = $firstObjectNumber;
        $this->nrOfEntries = $nrOfEntries;
    }

    public function addCrossReferenceEntry(CrossReferenceEntry $crossReferenceEntry): self
    {
        $this->crossReferenceEntries[] = $crossReferenceEntry;

        return $this;
    }
}
