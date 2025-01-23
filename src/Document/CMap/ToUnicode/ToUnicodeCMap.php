<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CMap\ToUnicode;

use PrinsFrank\PdfParser\Exception\ParseFailureException;

class ToUnicodeCMap {
    /** @var list<BFRange|BFChar> */
    public readonly array $bfCharRangeInfo;

    /** @no-named-arguments */
    public function __construct(
        public readonly int $codeSpaceStart,
        public readonly int $codeSpaceEnd,
        public readonly int $byteSize,
        BFRange|BFChar ...$bfCharRangeInfo,
    ) {
        $this->bfCharRangeInfo = $bfCharRangeInfo;
    }

    public function textToUnicode(string $characterCodes): string {
        return implode(
            '',
            array_map(
                fn (string $characterGroup) => $this->charToUnicode((int) hexdec($characterGroup))
                    ?? throw new ParseFailureException(sprintf('Unable to map character group "%s" to a unicode character', $characterGroup)),
                str_split(substr($characterCodes, 1, -1), $this->byteSize)
            )
        );
    }

    public function charToUnicode(int $characterCode): ?string {
        foreach ($this->bfCharRangeInfo as $bfCharRangeInfo) {
            if (!$bfCharRangeInfo->containsCharacterCode($characterCode)) {
                continue;
            }

            return $bfCharRangeInfo->toUnicode($characterCode);
        }

        if ($characterCode === 0) {
            return '';
        }

        return null;
    }
}
