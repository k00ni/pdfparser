<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryEntry\DictionaryEntryFactory;
use PrinsFrank\PdfParser\Document\Errors\ErrorCollection;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

class DictionaryFactory {
    /** @throws ParseFailureException */
    public static function fromArray(array $dictionaryArray, ErrorCollection $errorCollection): Dictionary {
        $dictionaryEntries = [];
        foreach ($dictionaryArray as $keyString => $value) {
            $dictionaryEntry = DictionaryEntryFactory::fromKeyValuePair($keyString, $value, $errorCollection);
            if ($dictionaryEntry === null) {
                continue;
            }

            $dictionaryEntries[] = $dictionaryEntry;
        }

        return new Dictionary(... $dictionaryEntries);
    }
}
