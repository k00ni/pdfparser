<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document;

use PrinsFrank\PdfParser\Document\CrossReferenceTable\CrossReferenceTable;
use PrinsFrank\PdfParser\Document\Trailer\Trailer;
use PrinsFrank\PdfParser\Enum\Version;

final class Document
{
    public readonly string $content;
    public readonly int    $contentLength;

    public readonly Version             $version;
    public readonly CrossReferenceTable $crossReferenceTable;
    public readonly Trailer             $trailer;

    public function __construct(string $content)
    {
        $this->content       = $content;
        $this->contentLength = strlen($content);
    }

    public function setTrailer(Trailer $trailer): self
    {
        $this->trailer = $trailer;

        return $this;
    }

    public function setVersion(Version $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function setCrossReferenceTable(CrossReferenceTable $crossReferenceTable): self
    {
        $this->crossReferenceTable = $crossReferenceTable;

        return $this;
    }
}
