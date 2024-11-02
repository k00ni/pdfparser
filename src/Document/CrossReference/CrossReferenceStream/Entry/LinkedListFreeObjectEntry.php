<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceStream\Entry;

/** 7.5.8, Table 18 */
class LinkedListFreeObjectEntry {
    public function __construct(
        public readonly int $objectNumberNextFreeObject,
        public readonly int $generationNumberIfUsedAgain,
    ) {
    }
}
