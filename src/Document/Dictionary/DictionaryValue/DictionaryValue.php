<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Array\ArrayValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Date\DateValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Integer\IntegerValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\TrappedNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Reference\ReferenceValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\FilterNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\TextString\TextStringValue;
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
            DictionaryKey::TRAPPED => TrappedNameValue::fromValue($valueString),
            DictionaryKey::INDEX,
            DictionaryKey::ID,
            DictionaryKey::W => ArrayValue::fromValue($valueString),
            DictionaryKey::LENGTH,
            DictionaryKey::COLUMNS,
            DictionaryKey::PREDICTOR,
            DictionaryKey::PREVIOUS,
            DictionaryKey::N,
            DictionaryKey::FIRST,
            DictionaryKey::SIZE => IntegerValue::fromValue($valueString),
            DictionaryKey::INFO,
            DictionaryKey::ROOT => ReferenceValue::fromValue($valueString),
            DictionaryKey::CREATOR,
            DictionaryKey::PTEX_FULL_BANNER,
            DictionaryKey::PRODUCER => TextStringValue::fromValue($valueString),
            DictionaryKey::MOD_DATE,
            DictionaryKey::CREATION_DATE => DateValue::fromValue($valueString),
            default => throw new ParseFailureException('Dictionary key "' . $dictionaryKey->name . '" is not supported'),
        };
    }
}
