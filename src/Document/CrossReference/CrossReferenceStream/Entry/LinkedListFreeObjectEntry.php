<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceStream\Entry;

class LinkedListFreeObjectEntry {
    public function __construct(
        public readonly int $objectNumberNextFreeObject,
        public readonly int $generationNumberIfGeneratedAgain,
    ) {
    }
}
