<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Array;

use Override;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryEntry\DictionaryEntry;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\DictionaryValueType;
use PrinsFrank\PdfParser\Exception\InvalidArgumentException;
use PrinsFrank\PdfParser\Exception\InvalidDictionaryValueTypeFormatException;
use PrinsFrank\PdfParser\Exception\RuntimeException;

class ArrayValue implements DictionaryValueType {
    /** @param array<mixed> $value */
    public function __construct(
        public readonly array $value
    ) {
    }

    #[Override]
    public static function fromValue(string $valueString): DictionaryValueType {
        if (str_starts_with($valueString, '[') === false || str_ends_with($valueString, ']') === false) {
            throw new InvalidDictionaryValueTypeFormatException('Invalid value for array: "' . $valueString . '", should start with "[" and end with "]".');
        }

        $array = [];
        $valueString = preg_replace('/(<[^>]*>)(?=<[^>]*>)/', '$1 $2', $valueString)
            ?? throw new RuntimeException('An error occured while sanitizing array value');
        $values = explode(' ', rtrim(ltrim($valueString, '[ '), ' ]'));
        foreach ($values as $value) {
            if (str_starts_with($value, '[') && str_ends_with($value, ']')) {
                $array[] = self::fromValue($value);
            } elseif ((string) (int) $value === $value) {
                $array[] = (int) $value;
            } else {
                $array[] = $value;
            }
        }

        return new self($array);
    }

    /**
     * @template T of DictionaryValueType
     * @param class-string<T> $valueType
     * @return T
     */
    public function getValueForKey(DictionaryKey $dictionaryKey, string $valueType): ?DictionaryValueType {
        foreach ($this->value as $entry) {
            if ($entry instanceof DictionaryEntry === false) {
                continue;
            }

            if ($entry->key === $dictionaryKey) {
                $value = $entry->value;
                if (is_a($value, $valueType) === false) {
                    throw new InvalidArgumentException(sprintf('Expected value with key %s to be of type %s, got %s', $dictionaryKey->name, $valueType, get_class($value)));
                }

                return $value;
            }
        }

        return null;
    }
}
