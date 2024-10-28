<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Trailer;

use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;

/**
 * PDF 32000-1:2008
 * The trailer of a PDF file enables a conforming reader to quickly find the cross-reference table and certain special objects
 */
final class Trailer {
    public function __construct(
        public readonly int         $eofMarkerPos,
        public readonly int         $startXrefMarkerPos,
        public readonly int         $byteOffsetLastCrossReferenceSection,
        public readonly int         $startTrailerMarkerPos,
        public readonly ?Dictionary $dictionary,
    ) {
    }
}
