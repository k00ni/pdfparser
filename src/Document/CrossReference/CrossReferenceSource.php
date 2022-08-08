<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceData\CrossReferenceData;

abstract class CrossReferenceSource
{
    /** @var array<CrossReferenceData> */
    public array $data = [];

    public function addData(CrossReferenceData $data): self
    {
        $this->data[] = $data;

        return $this;
    }
}
