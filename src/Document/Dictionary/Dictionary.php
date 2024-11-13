<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryEntry\DictionaryEntry;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\DictionaryValueType;
use PrinsFrank\PdfParser\Exception\InvalidArgumentException;

class Dictionary {
    /** @var array<DictionaryEntry> */
    public readonly array $dictionaryEntries;

    /** @no-named-arguments */
    public function __construct(
        DictionaryEntry... $dictionaryEntries
    ) {
        $this->dictionaryEntries = $dictionaryEntries;
    }

    /**
     * @template T of DictionaryValueType
     * @param class-string<T> $valueType
     * @return T
     */
    public function getValueForKey(DictionaryKey $dictionaryKey, string $valueType): ?DictionaryValueType {
        foreach ($this->dictionaryEntries as $dictionaryEntry) {
            if ($dictionaryEntry->key === $dictionaryKey) {
                $value = $dictionaryEntry->value;
                if (is_a($value, $valueType) === false) {
                    throw new InvalidArgumentException(sprintf('Expected value with key %s to be of type %s, got %s', $dictionaryKey->name, $valueType, get_class($value)));
                }

                return $value;
            }
        }

        return null;
    }
}
