<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryEntry;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValue;

class DictionaryEntryFactory
{
    public static function fromKeyValuePair(string $keyString, mixed $value): ?DictionaryEntry
    {
        $dictionaryKey = DictionaryKey::tryFromKeyString($keyString);
        if ($dictionaryKey === null) {
            return null;
        }

        return (new DictionaryEntry())->setKey($dictionaryKey)->setValue($value);
    }
}
