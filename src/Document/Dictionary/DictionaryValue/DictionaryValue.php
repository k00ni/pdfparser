<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Array\ArrayValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Integer\IntegerValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Reference\ReferenceValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\FilterNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\TypeNameValue;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

class DictionaryValue
{
    /**
     * @throws ParseFailureException
     */
    public static function fromValueString(DictionaryKey $dictionaryKey, string $valueString)
    {
        return match ($dictionaryKey) {
            DictionaryKey::FILTER => FilterNameValue::fromValue($valueString),
            DictionaryKey::TYPE => TypeNameValue::fromValue($valueString),
            DictionaryKey::INDEX,
            DictionaryKey::ID,
            DictionaryKey::W => ArrayValue::fromValue($valueString),
            DictionaryKey::LENGTH,
            DictionaryKey::COLUMNS,
            DictionaryKey::PREDICTOR,
            DictionaryKey::PREVIOUS,
            DictionaryKey::SIZE => IntegerValue::fromValue($valueString),
            DictionaryKey::INFO,
            DictionaryKey::ROOT => ReferenceValue::fromValue($valueString),
            default => throw new ParseFailureException('Dictionary key "' . $dictionaryKey->name . '" is not supported'),
        };
    }
}
