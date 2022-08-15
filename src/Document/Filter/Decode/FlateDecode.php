<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Filter\Decode;

use PrinsFrank\PdfParser\Exception\GzUncompressException;

class FlateDecode implements FilterDecoder
{
    public static function decode(string $value): string
    {
        $decodedValue = @gzuncompress(trim($value));
        if ($decodedValue === false) {
            throw new GzUncompressException($value);
        }

        return $decodedValue;
    }
}
