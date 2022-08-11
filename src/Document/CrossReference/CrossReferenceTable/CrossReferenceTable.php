<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceSource;

class CrossReferenceTable implements CrossReferenceSource
{
    /** @var array<CrossReferenceSubSection> */
    public readonly array $crossReferenceSubSections;

    public function __construct(array $crossReferenceSubSections)
    {
        $this->crossReferenceSubSections = $crossReferenceSubSections;
    }
}
