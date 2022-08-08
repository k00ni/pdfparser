<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name;

enum SubtypeNameValue: string implements NameValue
{
    case TYPE_1 = 'Type1';
    case IMAGE = 'Image';
    case XML = 'XML';
    case TRUE_TYPE = 'TrueType';
    case FORM = 'Form';

    public static function fromValue(string $valueString): self
    {
        return self::from(trim(ltrim($valueString, '/')));
    }
}
