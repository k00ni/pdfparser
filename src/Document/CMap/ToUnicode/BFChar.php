<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CMap\ToUnicode;

class BFChar {
    public function __construct(
        public readonly int $sourceCode,
        public readonly int $destinationString,
    ) {
    }

    public function toUnicode(int $characterCode): ?string {
        if ($characterCode !== $this->sourceCode) {
            return null;
        }

        $string = '';
        $hexString = dechex($this->destinationString);
        $hexString = str_pad($hexString, (int) ceil(strlen($hexString) / 4) * 4, '0', STR_PAD_LEFT);
        foreach (str_split($hexString, 4) as $char) {
            $string .= chr((int) hexdec($char));
        }

        return $string;
    }
}
