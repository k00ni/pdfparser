<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Text\Encoding;

interface Encoding {
    public static function textToUnicode(string $string): string;
}
