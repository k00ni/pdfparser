<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference;

interface CrossReferenceSource {
    /** @return list<int> */
    public function getByteOffsets(): array;
}
