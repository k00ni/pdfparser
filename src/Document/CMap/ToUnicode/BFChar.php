<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CMap\ToUnicode;

class BFChar {
    public function __construct(
        public readonly int $sourceCode,
        public readonly int $destinationString,
    ) {
    }
}
