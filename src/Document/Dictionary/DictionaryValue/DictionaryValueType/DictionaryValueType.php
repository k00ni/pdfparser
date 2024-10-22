<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType;

interface DictionaryValueType {
    public static function fromValue(string $valueString): self;
}
