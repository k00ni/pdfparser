<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object;

use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;

class ObjectItem {
    public function __construct(
        public readonly int $objectNumber,
        public readonly int $generationNumber,
        public readonly ?Dictionary $dictionary,
    ) {
    }
}
