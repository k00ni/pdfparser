<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryEntry;

use PrinsFrank\PdfParser\Enum\DictionaryKey;

class DictionaryEntry
{
    public readonly DictionaryKey $key;
    public readonly string $value;

    public function setKey(DictionaryKey $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
