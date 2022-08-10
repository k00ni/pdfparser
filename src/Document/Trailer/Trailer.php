<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Trailer;

use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;
use PrinsFrank\PdfParser\Document\Document;

/**
 * PDF 32000-1:2008
 * The trailer of a PDF file enables a conforming reader to quickly find the cross-reference table and certain special objects
 */
final class Trailer
{
    public readonly Document    $document;
    public readonly int         $eofMarkerPos;
    public readonly int         $startXrefMarkerPos;
    public readonly int         $byteOffsetLastCrossReferenceSection;
    public readonly ?int        $startTrailerMarkerPos;
    public readonly ?Dictionary $dictionary;

    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    public function setEofMarkerPos(int $eofMarkerPos): self
    {
        $this->eofMarkerPos = $eofMarkerPos;

        return $this;
    }

    public function setStartXrefMarkerPos(int $startXrefMarkerPos): self
    {
        $this->startXrefMarkerPos = $startXrefMarkerPos;

        return $this;
    }

    public function setByteOffsetLastCrossReferenceSection(mixed $byteOffsetLastCrossReferenceSection): self
    {
        $this->byteOffsetLastCrossReferenceSection = $byteOffsetLastCrossReferenceSection;

        return $this;
    }

    public function setStartTrailerMarkerPos(?int $startTrailerMarkerPos): self
    {
        $this->startTrailerMarkerPos = $startTrailerMarkerPos;

        return $this;
    }

    public function setDictionary(?Dictionary $dictionary): self
    {
        $this->dictionary = $dictionary;

        return $this;
    }
}
