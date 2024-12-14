<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\Decorator;

use DateTimeImmutable;
use Override;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Date\DateValue;
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

    public function getCreationDate(): ?DateTimeImmutable {
        return $this->getDictionary($this->stream)
            ->getValueForKey(DictionaryKey::CREATION_DATE, DateValue::class)
            ?->value;
    }

    public function getModificationDate(): ?DateTimeImmutable {
        return $this->getDictionary($this->stream)
            ->getValueForKey(DictionaryKey::MOD_DATE, DateValue::class)
            ?->value;
    }

    #[Override]
    protected function getTypeName(): ?TypeNameValue {
        return null;
    }
}
