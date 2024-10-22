<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Array;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\DictionaryValueType;
use PrinsFrank\PdfParser\Exception\InvalidDictionaryValueTypeFormatException;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

class ArrayValue implements DictionaryValueType
{
    public function __construct(public array $value)
    {
    }

    /** @throws ParseFailureException */
    public static function fromValue(string $valueString): DictionaryValueType
    {
        if (str_starts_with($valueString, '[') === false || str_ends_with($valueString, ']') === false) {
            throw new InvalidDictionaryValueTypeFormatException('Invalid value for array: "' . $valueString . '", should start with "[" and end with "]".');
        }

        $array = [];
        $valueString = preg_replace('/(<[^>]*>)(?=<[^>]*>)/', '$1 $2', $valueString);
        $values = explode(' ', rtrim(ltrim($valueString, '['), ']'));
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
}
