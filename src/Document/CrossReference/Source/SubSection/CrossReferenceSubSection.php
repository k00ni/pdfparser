<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference\Source\SubSection;

use PrinsFrank\PdfParser\Document\CrossReference\Source\SubSection\Entry\CompressedObjectEntry;
use PrinsFrank\PdfParser\Document\CrossReference\Source\SubSection\Entry\CrossReferenceEntryFreeObject;
use PrinsFrank\PdfParser\Document\CrossReference\Source\SubSection\Entry\CrossReferenceEntryInUseObject;
use RuntimeException;

class CrossReferenceSubSection {
    /** @var array<CrossReferenceEntryInUseObject|CrossReferenceEntryFreeObject|CompressedObjectEntry> */
    public array $crossReferenceEntries = [];

    public function __construct(
        public readonly int $firstObjectNumber,
        public readonly int $nrOfEntries,
        CrossReferenceEntryInUseObject|CrossReferenceEntryFreeObject|CompressedObjectEntry... $crossReferenceEntries
    ) {
        //        if ($this->nrOfEntries !== count($crossReferenceEntries)) {
        //            throw new InvalidArgumentException(sprintf('Cross reference subsection defines %d entries, got %d', $this->nrOfEntries, count($crossReferenceEntries)));
        //        }

        $this->crossReferenceEntries = $crossReferenceEntries;
    }

    public function containsObject(int $objNumber): bool {
        return $objNumber >= $this->firstObjectNumber
            && $objNumber < $this->firstObjectNumber + $this->nrOfEntries;
    }

    public function getCrossReferenceEntry(int $objNumber): ?CrossReferenceEntryInUseObject {
        if (self::containsObject($objNumber) === false) {
            return null;
        }

        return $this->crossReferenceEntries[$objNumber - $this->firstObjectNumber] ?? throw new RuntimeException(sprintf('Object with key %d should exist when self::containsObject is true', $objNumber - $this->firstObjectNumber));
    }

    /** @return list<int> */
    public function getByteOffsets(): array {
        $byteOffsets = [];
        foreach ($this->crossReferenceEntries as $crossReferenceEntry) {
            if ($crossReferenceEntry instanceof CrossReferenceEntryInUseObject) {
                $byteOffsets[] = $crossReferenceEntry->byteOffsetInDecodedStream;
            }
        }

        return $byteOffsets;
    }
}
