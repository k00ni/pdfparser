<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Filter\Decode;

use PrinsFrank\PdfParser\Exception\PdfParserException;

interface FilterDecoder {
    /** @throws PdfParserException */
    public static function decode(string $value): string;
}
