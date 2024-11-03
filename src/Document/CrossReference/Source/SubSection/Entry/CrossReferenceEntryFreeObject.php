<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference\Source\SubSection\Entry;

/** Present in both crossReferenceTable and crossReferenceStream */
class CrossReferenceEntryFreeObject {
    /**
     * @param int<0, 9999999999> $objectNumberNextFreeObject
     * @param int<0, 99999> $generationNumber
     */
    public function __construct(
        public readonly int $objectNumberNextFreeObject,
        public readonly int $generationNumber,
    ) {
    }
}
