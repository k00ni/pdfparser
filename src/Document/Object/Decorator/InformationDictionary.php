<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\Decorator;

use Override;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\TextString\TextStringValue;

class InformationDictionary extends DecoratedObject {
    public function getTitle(): ?string {
        return $this->getDictionary($this->stream)
            ->getValueForKey(DictionaryKey::TITLE, TextStringValue::class)
            ?->textStringValue;
    }

    public function getProducer(): ?string {
        return $this->getDictionary($this->stream)
            ->getValueForKey(DictionaryKey::PRODUCER, TextStringValue::class)
            ?->textStringValue;
    }

    public function getAuthor(): ?string {
        return $this->getDictionary($this->stream)
            ->getValueForKey(DictionaryKey::AUTHOR, TextStringValue::class)
            ?->textStringValue;
    }

    public function getCreator(): ?string {
        return $this->getDictionary($this->stream)
            ->getValueForKey(DictionaryKey::CREATOR, TextStringValue::class)
            ?->textStringValue;
    }

    #[Override]
    protected function getTypeName(): ?TypeNameValue {
        return null;
    }
}
