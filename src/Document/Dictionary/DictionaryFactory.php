<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryEntry\DictionaryEntryFactory;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

class DictionaryFactory
{
    /**
     * @throws ParseFailureException
     */
    public static function fromArray(array $dictionaryArray): Dictionary
    {
        $dictionary = new Dictionary();
        foreach ($dictionaryArray as $keyString => $value) {
            $dictionaryEntry = DictionaryEntryFactory::fromKeyValuePair($keyString, $value);
            if ($dictionaryEntry === null) {
                continue;
            }

            $dictionary->addEntry($dictionaryEntry);
        }

        return $dictionary;
    }
}
