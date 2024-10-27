<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Unused;

use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;

class ObjectItem {
    public function __construct(
        public int        $objectNumber,
        public string     $content,
        public Dictionary $dictionary
    ) {
    }
}
