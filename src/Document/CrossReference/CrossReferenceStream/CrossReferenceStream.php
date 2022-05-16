<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceStream;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceData\CrossReferenceData;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceSource;

class CrossReferenceStream implements CrossReferenceSource
{
    /** @var array<CrossReferenceData> */
    public array $data = [];

    public function addData(CrossReferenceData $data): self
    {
        $this->data[] = $data;

        return $this;
    }
}
