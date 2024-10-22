<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceStream\Entry;

class CompressedObjectEntry {
    /**
     * @see Table 18
     *
     * The object number of the object stream in which this object is
     * stored. (The generation number of the object stream shall be
     * implicitly 0.)
     */
    final public const GENERATION_NUMBER = 0;

    public function __construct(
        public readonly int $storedInObjectNumber,
        public readonly int $indexOfThisObjectWithinObjectStream,
    ) {
    }
}
