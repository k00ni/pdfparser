<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CMap\Registry;

use PrinsFrank\PdfParser\Document\CMap\ToUnicode\ToUnicodeCMap;

interface CMapResource {
    public function getToUnicodeCMap(): ToUnicodeCMap;
}
