<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceStream;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceSource;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceStream\Entry\CompressedObjectEntry;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceStream\Entry\LinkedListFreeObjectEntry;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceStream\Entry\UncompressedDataEntry;

class CrossReferenceStream implements CrossReferenceSource {
    /** @var array<CompressedObjectEntry|LinkedListFreeObjectEntry|UncompressedDataEntry> */
    public array $entries = [];

    public function addEntry(CompressedObjectEntry|LinkedListFreeObjectEntry|UncompressedDataEntry $data): self {
        $this->entries[] = $data;

        return $this;
    }

    public function getByteOffsets(): array {
        $byteOffsets = [];
        foreach ($this->entries as $crossReferenceData) {
            if ($crossReferenceData instanceof UncompressedDataEntry) {
                $byteOffsets[] = $crossReferenceData->objNumberOrByteOffset;
            }
        }

        return $byteOffsets;
    }
}
