<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\Decorator;

use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Object\Item\ObjectItem;
use PrinsFrank\PdfParser\Exception\InvalidArgumentException;
use PrinsFrank\PdfParser\Stream;

abstract class DecoratedObject {
    final public function __construct(
        public readonly ObjectItem $objectItem,
        protected readonly Stream $stream
    ) {
        $typeNameValue = $this->objectItem->getDictionary($stream)->getValueForKey(DictionaryKey::TYPE, TypeNameValue::class);
        if ($typeNameValue !== null && $typeNameValue !== $this->getTypeName()) {
            throw new InvalidArgumentException(
                sprintf('Expected object item of type %s, got %s', $this->getTypeName()->name ?? 'UNKNOWN', $typeNameValue->name ?? 'UNKNOWN')
            );
        }
    }

    public function getDictionary(): Dictionary {
        return $this->objectItem->getDictionary($this->stream);
    }

    abstract protected function getTypeName(): ?TypeNameValue;
}
