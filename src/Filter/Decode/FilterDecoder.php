<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Filter\Decode;

interface FilterDecoder
{
    public static function decode(string $value): string;
}
