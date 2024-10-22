<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name;

enum TypeNameValue: string implements NameValue
{
    case ANNOT = 'Annot';
    case CATALOG = 'Catalog';
    case ENCODING = 'Encoding';
    case EXT_G_STATE = 'ExtGState';
    case FONT = 'Font';
    case FONT_DESCRIPTOR = 'FontDescriptor';
    case GROUP = 'Group';
    case METADATA = 'Metadata';
    case OBJ_STM = 'ObjStm';
    case OUTLINES = 'Outlines';
    case PAGE = 'Page';
    case PAGES = 'Pages';
    case STREAM = 'Stream';
    case X_OBJECT = 'XObject';
    case X_REF = 'XRef';

    public static function fromValue(string $valueString): self
    {
        return self::from(trim(ltrim($valueString, '/')));
    }
}
