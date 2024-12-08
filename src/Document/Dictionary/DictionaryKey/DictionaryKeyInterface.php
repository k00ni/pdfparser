<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey;

use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name\NameValue;

interface DictionaryKeyInterface {
    /** @return list<class-string<DictionaryValue|Dictionary|NameValue>> */
    public function getValueTypes(): array;
}
