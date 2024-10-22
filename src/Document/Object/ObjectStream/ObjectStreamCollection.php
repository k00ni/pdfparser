<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\ObjectStream;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Reference\ReferenceValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Reference\ReferenceValueArray;
use PrinsFrank\PdfParser\Document\Object\ObjectItem;

class ObjectStreamCollection {
    /** @var list<ObjectStream> */
    private readonly array $objectStreams;

    /** @param list<ObjectStream> */
    public function __construct(ObjectStream ...$objectStreams) {
        $this->objectStreams = $objectStreams;
    }

    /** @return array<ObjectStream> */
    public function getObjectStreamsByType(TypeNameValue $typeNameValue): array {
        $objectStreams = [];
        foreach ($this->objectStreams as $objectStream) {
            if ($objectStream->dictionary->getEntryWithKey(DictionaryKey::TYPE)?->value === $typeNameValue) {
                $objectStreams[] = $objectStream;
            }
        }

        return $objectStreams;
    }

    public function getObjectByReference(ReferenceValue $referenceValue): ObjectItem|ObjectStream|null {
        foreach ($this->objectStreams as $objectStream) {
            if ($objectStream->objectNumber === $referenceValue->objectNumber) {
                return $objectStream;
            }

            foreach ($objectStream->objectItemCollection->objectItems as $objectItem) {
                if ($objectItem->objectNumber === $referenceValue->objectNumber) {
                    return $objectItem;
                }
            }
        }

        return null;
    }

    /** @return list<ObjectItem|ObjectStream> */
    public function getObjectsByReference(ReferenceValueArray $referenceValue): array {
        $objects = [];
        foreach ($this->objectStreams as $objectStream) {
            if (in_array($objectStream->objectNumber, $referenceValue->objectNumbers(), true)) {
                $objects[] = $objectStream;

                continue;
            }

            foreach ($objectStream->objectItemCollection->objectItems as $objectItem) {
                if (in_array($objectItem->objectNumber, $referenceValue->objectNumbers(), true)) {
                    $objects[] = $objectItem;
                }
            }
        }

        return $objects;
    }
}
