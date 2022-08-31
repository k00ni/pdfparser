<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Page;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Object\ObjectItem;
use PrinsFrank\PdfParser\Exception\InvalidArgumentException;

class Page
{
    private readonly ObjectItem $page;
    private readonly ObjectItem $content;

    /** @throws InvalidArgumentException */
    public function __construct(ObjectItem $page, ObjectItem $content)
    {
        if ($page->dictionary->getEntryWithKey(DictionaryKey::TYPE)?->value !== TypeNameValue::PAGE) {
            throw new InvalidArgumentException();
        }

        $this->page = $page;
        $this->content = $content;
    }
}
