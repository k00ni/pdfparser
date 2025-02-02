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

    public function containsCharacterCode(int $characterCode): bool {
        return $characterCode >= $this->sourceCodeStart
            && $characterCode <= $this->sourceCodeEnd;
    }

    public function toUnicode(int $characterCode): ?string {
        if (count($this->destinationString) === 1) {
            return mb_chr($this->destinationString[0] + $characterCode - $this->sourceCodeStart);
        }

        return mb_chr(
            $this->destinationString[$characterCode - $this->sourceCodeStart]
                ?? throw new RuntimeException(sprintf('Character code %d was not found in BFRange of length %d with start %d and end %d', $characterCode, count($this->destinationString), $this->sourceCodeStart, $this->sourceCodeEnd))
        );
    }
}
