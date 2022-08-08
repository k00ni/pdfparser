<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name;

enum TypeNameValue: string implements NameValue
{
    case FONT            = 'Font';
    case FONT_DESCRIPTOR = 'FontDescriptor';
    case GROUP           = 'Group';
    case OBJ_STM         = 'ObjStm';
    case PAGE            = 'Page';
    case PAGES           = 'Pages';
    case X_OBJECT        = 'XObject';
    case X_REF           = 'XRef';

    public static function fromValue(string $valueString): self
    {
        return self::from(trim(ltrim($valueString, '/')));
    }
}
