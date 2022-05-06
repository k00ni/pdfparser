<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document;

final class Trailer
{
    private readonly Document $document;
    private readonly int      $eofMarkerPos;
    private readonly int      $startXrefMarkerPos;
    private readonly int      $byteOffsetLastCrossReferenceSection;

    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    public function setEofMarkerPos(int $eofMarkerPos): void
    {
        $this->eofMarkerPos = $eofMarkerPos;
    }

    public function setStartXrefMarkerPos(int $startXrefMarkerPos): void
    {
        $this->startXrefMarkerPos = $startXrefMarkerPos;
    }

    public function setByteOffsetLastCrossReferenceSection(mixed $byteOffsetLastCrossReferenceSection): void
    {
        $this->byteOffsetLastCrossReferenceSection = $byteOffsetLastCrossReferenceSection;
    }
}
