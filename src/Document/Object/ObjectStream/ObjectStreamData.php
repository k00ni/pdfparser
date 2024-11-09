<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\ObjectStream;

class ObjectStreamData {
    /** @param array<int, int> $objectNumberByteOffsets */
    public function __construct(
        private readonly array $objectNumberByteOffsets,
    ) { }

    public function getRelativeByteOffsetForObject(int $objNumber): ?int {
        return $this->objectNumberByteOffsets[$objNumber] ?? null;
    }
}