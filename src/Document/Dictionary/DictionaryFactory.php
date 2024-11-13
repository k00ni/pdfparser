<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryEntry\DictionaryEntryFactory;

class DictionaryFactory {
    public static function fromArray(array $dictionaryArray): Dictionary {
        $dictionaryEntries = [];
        foreach ($dictionaryArray as $keyString => $value) {
            $dictionaryEntry = DictionaryEntryFactory::fromKeyValuePair($keyString, $value);
            if ($dictionaryEntry === null) {
                continue;
            }

            $dictionaryEntries[] = $dictionaryEntry;
        }

        return new Dictionary(... $dictionaryEntries);
    }
}
