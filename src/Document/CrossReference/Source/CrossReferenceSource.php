<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference\Source;

use PrinsFrank\PdfParser\Document\CrossReference\Source\SubSection\CrossReferenceSubSection;
use PrinsFrank\PdfParser\Document\CrossReference\Source\SubSection\Entry\CrossReferenceEntryCompressed;
use PrinsFrank\PdfParser\Document\CrossReference\Source\SubSection\Entry\CrossReferenceEntryInUseObject;
use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;

/** Can be both from a crossReferenceTable or a crossReferenceStream */
class CrossReferenceSource {
    /** @param list<CrossReferenceSubSection> $crossReferenceSubSections */
    public readonly array $crossReferenceSubSections;

    public function __construct(
        public readonly ?Dictionary $dictionary,
        CrossReferenceSubSection... $crossReferenceSubSections,
    ) {
        $this->crossReferenceSubSections = $crossReferenceSubSections;
    }

    public function getCrossReferenceEntry(int $objNumber): CrossReferenceEntryInUseObject|CrossReferenceEntryCompressed|null {
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
