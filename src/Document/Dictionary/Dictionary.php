<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary;

use JsonSerializable;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryEntry\DictionaryEntry;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;

class Dictionary implements JsonSerializable
{
    /** @var array<DictionaryEntry> */
    protected array $dictionaryEntries = [];

    public function addEntry(DictionaryEntry $dictionaryEntry): self
    {
        $this->dictionaryEntries[] = $dictionaryEntry;

        return $this;
    }

    /** @return array<DictionaryEntry> */
    public function getDictionaryEntries(): array
    {
        return $this->dictionaryEntries;
    }

    public function getEntryWithKey(DictionaryKey $dictionaryKey): ?DictionaryEntry
    {
        foreach ($this->dictionaryEntries as $dictionaryEntry) {
            if ($dictionaryEntry->key === $dictionaryKey) {
                return $dictionaryEntry;
            }
        }

        return null;
    }

    public function jsonSerialize(): array
    {
        return array_map(
            static function (DictionaryEntry $dictionaryEntry) {
                return (array) $dictionaryEntry;
            },
            $this->dictionaryEntries
        );
    }
}
