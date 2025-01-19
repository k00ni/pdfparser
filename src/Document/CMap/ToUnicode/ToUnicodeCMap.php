<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CMap\ToUnicode;

class ToUnicodeCMap {
    /** @var list<BFRange|BFChar> */
    public readonly array $bfCharRangeInfo;

    /** @no-named-arguments */
    public function __construct(
        public readonly int $codeSpaceStart,
        public readonly int $codeSpaceEnd,
        BFRange|BFChar ...$bfCharRangeInfo,
    ) {
        $this->bfCharRangeInfo = $bfCharRangeInfo;
    }

    public function toUnicode(int $characterCode): ?string {
        foreach ($this->bfCharRangeInfo as $bfCharRangeInfo) {
            if (!$bfCharRangeInfo->containsCharacterCode($characterCode)) {
                continue;
            }

            return $bfCharRangeInfo->toUnicode($characterCode);
        }

        return null;
    }
}
