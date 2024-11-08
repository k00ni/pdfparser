<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryEntry\DictionaryEntry;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\DictionaryValueType;

class Dictionary {
    /** @var array<DictionaryEntry> */
    public readonly array $dictionaryEntries;

    public function __construct(
        DictionaryEntry... $dictionaryEntries
    ) {
        $this->dictionaryEntries = $dictionaryEntries;
    }

    public function getValueForKey(DictionaryKey $dictionaryKey): ?DictionaryValueType {
        foreach ($this->dictionaryEntries as $dictionaryEntry) {
            if ($dictionaryEntry->key === $dictionaryKey) {
                return $dictionaryEntry->value;
            }
        }

        return null;
    }
}
