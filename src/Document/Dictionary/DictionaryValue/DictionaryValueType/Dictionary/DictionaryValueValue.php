<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Dictionary;

use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParser;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\DictionaryValueType;

class DictionaryValueValue implements DictionaryValueType
{
    public function __construct(public readonly Dictionary $value) { }

    public static function fromValue(string $valueString): self
    {
        return new self(DictionaryParser::parse($valueString));
    }
}
