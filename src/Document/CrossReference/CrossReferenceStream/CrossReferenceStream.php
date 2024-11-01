<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceStream;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceSource;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceStream\Entry\CompressedObjectEntry;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceStream\Entry\LinkedListFreeObjectEntry;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceStream\Entry\NullObjectEntry;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceStream\Entry\UncompressedDataEntry;
use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;

class CrossReferenceStream implements CrossReferenceSource {
    /** @var array<CompressedObjectEntry|LinkedListFreeObjectEntry|UncompressedDataEntry|NullObjectEntry> */
    public readonly array $entries;

    public function __construct(
        public readonly Dictionary $dictionary,
        CompressedObjectEntry|LinkedListFreeObjectEntry|UncompressedDataEntry|NullObjectEntry... $entries
    ) {
        $this->entries = $entries;
    }
}
