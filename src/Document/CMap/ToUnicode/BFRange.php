<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CMap\ToUnicode;

use PrinsFrank\PdfParser\Exception\RuntimeException;

class BFRange {
    /** @param list<int> $destinationString */
    public function __construct(
        public readonly int $sourceCodeStart,
        public readonly int $sourceCodeEnd,
        public readonly array $destinationString,
    ) {
    }

    public function toUnicode(int $characterCode): ?string {
        if ($characterCode >= $this->sourceCodeStart && $characterCode <= $this->sourceCodeEnd) {
            if (count($this->destinationString) === 1) {
                return chr($characterCode - $this->sourceCodeStart + $this->destinationString[0]);
            }

            return chr($this->destinationString[$this->sourceCodeStart - $characterCode] ?? throw new RuntimeException());
        }

        return null;
    }
}
