<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CMap\ToUnicode;

class BFRange {
    /** @param list<int> $destinationString */
    public function __construct(
        public readonly int $sourceCodeStart,
        public readonly int $sourceCodeEnd,
        public readonly array $destinationString,
    ) {
    }
}
