<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name;

enum TypeNameValue: string implements NameValue
{
    case OBJ_STM         = 'ObjStm';
    case X_REF           = 'XRef';
    case PAGE            = 'Page';
    case FONT_DESCRIPTOR = 'FontDescriptor';
    case FONT            = 'Font';
    case PAGES           = 'Pages';

    public static function fromValue(string $valueString): self
    {
        return self::from(trim(ltrim($valueString, '/')));
    }
}
