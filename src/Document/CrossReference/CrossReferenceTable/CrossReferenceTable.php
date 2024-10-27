<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceSource;

class CrossReferenceTable implements CrossReferenceSource {
    /** @param array<CrossReferenceSubSection> $crossReferenceSubSections */
    public readonly array $crossReferenceSubSections;

    public function __construct(
        CrossReferenceSubSection... $crossReferenceSubSections,
    ) {
        $this->crossReferenceSubSections = $crossReferenceSubSections;
    }

    public function getByteOffsets(): array {
        $byteOffsets = [];
        foreach ($this->crossReferenceSubSections as $crossReferenceSubSection) {
            foreach ($crossReferenceSubSection->crossReferenceEntries as $crossReferenceEntry) {
                $byteOffsets[] = $crossReferenceEntry->offset;
            }
        }

        return $byteOffsets;
    }
}
