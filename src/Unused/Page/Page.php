<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Unused\Page;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\TypeNameValue;
use PrinsFrank\PdfParser\Exception\InvalidArgumentException;
use PrinsFrank\PdfParser\Unused\Object\ObjectItem\ObjectItem;
use PrinsFrank\PdfParser\Unused\Object\ObjectStream\ObjectStream;

class Page {
    /** @var list<ObjectItem|ObjectStream> */
    private readonly array $contentObjects;

    /** @throws InvalidArgumentException */
    public function __construct(
        private readonly ObjectItem|ObjectStream $pageObject,
        ObjectItem|ObjectStream... $contentObjects
    ) {
        if ($pageObject->dictionary->getEntryWithKey(DictionaryKey::TYPE)?->value !== TypeNameValue::PAGE) {
            throw new InvalidArgumentException();
        }

        $this->contentObjects = $contentObjects;
    }
}
