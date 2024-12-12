<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryEntry\DictionaryEntry;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\ExtendedDictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name\NameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Reference\ReferenceValue;
use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Exception\InvalidArgumentException;
use PrinsFrank\PdfParser\Exception\ParseFailureException;
use PrinsFrank\PdfParser\Exception\RuntimeException;

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
     * @template T of DictionaryValue|NameValue|Dictionary
     * @param class-string<T> $valueType
     * @return T
     */
    public function getValueForKey(DictionaryKey $dictionaryKey, string $valueType): DictionaryValue|Dictionary|NameValue|null {
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

    /** @return class-string<DictionaryValue|NameValue|Dictionary> */
    public function getTypeForKey(DictionaryKey $dictionaryKey): ?string {
        foreach ($this->dictionaryEntries as $dictionaryEntry) {
            if ($dictionaryEntry->key === $dictionaryKey) {
                return $dictionaryEntry->value::class;
            }
        }

        return null;
    }

    public function getSubDictionary(Document $document, DictionaryKey $dictionaryKey): Dictionary {
        $subDictionaryType = $this->getTypeForKey($dictionaryKey);
        if ($subDictionaryType === Dictionary::class) {
            return $this->getValueForKey($dictionaryKey, Dictionary::class) ?? throw new RuntimeException();
        }

        if ($subDictionaryType === ReferenceValue::class) {
            $reference = $this->getValueForKey($dictionaryKey, ReferenceValue::class) ?? throw new RuntimeException();

            return ($document->getObject($reference->objectNumber) ?? throw new ParseFailureException())
                ->getDictionary($document->stream);
        }

        throw new ParseFailureException(sprintf('Invalid type %s for subDictionary', $subDictionaryType));
    }
}
