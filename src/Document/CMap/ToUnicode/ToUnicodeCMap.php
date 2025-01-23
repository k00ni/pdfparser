<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CMap\ToUnicode;

use PrinsFrank\PdfParser\Exception\InvalidArgumentException;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

class ToUnicodeCMap {
    /** @var list<BFRange|BFChar> */
    public readonly array $bfCharRangeInfo;

    /**
     * @no-named-arguments
     *
     * @param int<1, max> $byteSize
     */
    public function __construct(
        public readonly int $codeSpaceStart,
        public readonly int $codeSpaceEnd,
        public readonly int $byteSize,
        BFRange|BFChar ...$bfCharRangeInfo,
    ) {
        $this->bfCharRangeInfo = $bfCharRangeInfo;
        if ($this->byteSize < 1) {
            throw new InvalidArgumentException();
        }
    }

    public function textToUnicode(string $characterCodes): string {
        return implode(
            '',
            array_map(
                fn (string $characterGroup) => $this->charToUnicode((int) hexdec($characterGroup))
                    ?? throw new ParseFailureException(sprintf('Unable to map character group "%s" to a unicode character', $characterGroup)),
                str_split($characterCodes, $this->byteSize)
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
