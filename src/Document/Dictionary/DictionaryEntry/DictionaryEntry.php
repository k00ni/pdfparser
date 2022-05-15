<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryEntry;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\DictionaryValueType;

class DictionaryEntry
{
    public readonly DictionaryKey $key;
    public readonly DictionaryValueType $value;

    public function setKey(DictionaryKey $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function setValue(DictionaryValueType $value): self
    {
        $this->value = $value;

        return $this;
    }
}
