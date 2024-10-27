<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\ObjectStream;

use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;

class ObjectStream {
    public function __construct(
        public int                 $objectNumber,
        public int                 $generationNumber,
        public readonly Dictionary $dictionary,
    ) {
    }
}
