<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference\Source\SubSection\Entry;

/** Present in both crossReferenceTable and crossReferenceStream */
class CrossReferenceEntryInUseObject {
    /**
     * @param int<0, 9999999999> $byteOffsetInDecodedStream
     * @param int<0, 99999> $generationNumber
     */
    public function __construct(
        public readonly int $byteOffsetInDecodedStream,
        public readonly int $generationNumber,
    ) {
    }
}
