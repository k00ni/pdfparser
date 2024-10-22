<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\ObjectStream;

use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;
use PrinsFrank\PdfParser\Document\Object\ObjectItemCollection;

class ObjectStream {
    public function __construct(
        public int                           $objectNumber,
        public int                           $generationNumber,
        public readonly string               $content,
        public readonly ?string              $decodedStream,
        public readonly ObjectItemCollection $objectItemCollection,
        public readonly Dictionary           $dictionary,
    ) {
    }
}
