<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\ObjectStream;

use PrinsFrank\PdfParser\Stream;

class ObjectStreamData {
    /** @param array<int, int> $objectNumberByteOffsets */
    public function __construct(
        public readonly array $objectNumberByteOffsets,
        private readonly Stream $streamContent,
    ) {
    }

    private function getRelativeByteOffsetForObject(int $objNumber): ?int {
        return $this->objectNumberByteOffsets[$objNumber] ?? null;
    }

    private function getNextByteOffset(int $currentByteOffset): ?int {
        $byteOffsets = array_values($this->objectNumberByteOffsets);
        sort($byteOffsets);
        foreach ($byteOffsets as $byteOffset) {
            if ($byteOffset > $currentByteOffset) {
                return $byteOffset;
            }
        }

        return null;
    }

    public function getObjectStreamItem(int $objNumber): ?ObjectStreamItem {
        return new ObjectStreamItem(
            $this->streamContent,
            $byteOffsetStart = $this->getRelativeByteOffsetForObject($objNumber),
            $this->getNextByteOffset($byteOffsetStart),
        );
    }
}
