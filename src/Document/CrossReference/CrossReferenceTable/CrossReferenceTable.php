<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceSource;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable\Entry\CrossReferenceEntryInUseObject;

class CrossReferenceTable implements CrossReferenceSource {
    /** @param list<CrossReferenceSubSection> $crossReferenceSubSections */
    public readonly array $crossReferenceSubSections;

    public function __construct(
        CrossReferenceSubSection... $crossReferenceSubSections,
    ) {
        $this->crossReferenceSubSections = $crossReferenceSubSections;
    }

    public function getCrossReferenceEntry(int $objNumber): ?CrossReferenceEntryInUseObject {
        foreach ($this->crossReferenceSubSections as $crossReferenceSubSection) {
            if ($crossReferenceSubSection->containsObject($objNumber)) {
                return $crossReferenceSubSection->getCrossReferenceEntry($objNumber);
            }
        }

        return null;
    }

    public function getNextByteOffset(int $currentByteOffset): ?int {
        $byteOffsets = [];
        foreach ($this->crossReferenceSubSections as $crossReferenceSubSection) {
            $byteOffsets = [... $byteOffsets, ...$crossReferenceSubSection->getByteOffsets()];
        }

        sort($byteOffsets);
        foreach ($byteOffsets as $byteOffset) {
            if ($byteOffset > $currentByteOffset) {
                return $byteOffset;
            }
        }

        return null;
    }
}
