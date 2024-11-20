<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name;

enum EncodingNameValue: string implements NameValue {
    case MacRomanEncoding = 'MacRomanEncoding';
    case MacExpertEncoding = 'MacExpertEncoding';
    case WinAnsiEncoding = 'WinAnsiEncoding';
}
