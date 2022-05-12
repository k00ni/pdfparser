<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryEntry\DictionaryEntry;

class Dictionary
{
    /** @var array<DictionaryEntry> */
    protected array $dictionaryEntries = [];

    public function addEntry(DictionaryEntry $dictionaryEntry): void
    {
        $this->dictionaryEntries[] = $dictionaryEntry;
    }

    /** @return array<DictionaryEntry> */
    public function getDictionaryEntries(): array
    {
        return $this->dictionaryEntries;
    }
}
