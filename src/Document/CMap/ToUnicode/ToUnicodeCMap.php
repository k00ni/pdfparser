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

    public function textToUnicode(string $characterGroup): string {
        return implode(
            '',
            array_map(
                fn (string $character) => $this->charToUnicode((int) hexdec($character))
                    ?? throw new ParseFailureException(sprintf('Unable to map character group "%s" to a unicode character (byte size: %d)', $character, $this->byteSize)),
                str_split($characterGroup, $this->byteSize * 2)
            )
        );
    }

    protected function charToUnicode(int $characterCode): ?string {
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
