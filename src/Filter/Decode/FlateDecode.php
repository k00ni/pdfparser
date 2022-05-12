<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Filter\Decode;

class FlateDecode implements FilterDecoder
{
    public static function decode(string $value): string
    {
        return bin2hex(gzuncompress(trim($value)));
    }
}
