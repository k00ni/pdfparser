<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceStream\Entry;

class CompressedObjectEntry
{
    public function __construct(
        public readonly int $storedInObjectNumber,
        public readonly int $indexOfThisObjectWithinObjectStream,
    ) {
    }
}