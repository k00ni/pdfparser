<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name;

use PrinsFrank\PdfParser\Document\Object\Decorator\Annot;
use PrinsFrank\PdfParser\Document\Object\Decorator\Catalog;
use PrinsFrank\PdfParser\Document\Object\Decorator\DecoratedObject;
use PrinsFrank\PdfParser\Document\Object\Decorator\Encoding;
use PrinsFrank\PdfParser\Document\Object\Decorator\ExtGState;
use PrinsFrank\PdfParser\Document\Object\Decorator\Font;
use PrinsFrank\PdfParser\Document\Object\Decorator\FontDescriptor;
use PrinsFrank\PdfParser\Document\Object\Decorator\Group;
use PrinsFrank\PdfParser\Document\Object\Decorator\MarkInfo;
use PrinsFrank\PdfParser\Document\Object\Decorator\MetaData;
use PrinsFrank\PdfParser\Document\Object\Decorator\ObjectStream;
use PrinsFrank\PdfParser\Document\Object\Decorator\Outlines;
use PrinsFrank\PdfParser\Document\Object\Decorator\Page;
use PrinsFrank\PdfParser\Document\Object\Decorator\Pages;
use PrinsFrank\PdfParser\Document\Object\Decorator\StreamObject;
use PrinsFrank\PdfParser\Document\Object\Decorator\ViewerPreferences;
use PrinsFrank\PdfParser\Document\Object\Decorator\XObject;
use PrinsFrank\PdfParser\Document\Object\Decorator\XRef;

enum TypeNameValue: string implements NameValue {
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
    case MARK_INFO = 'MarkInfo';
    case VIEWER_PREFERENCES = 'ViewerPreferences';

    /** @return class-string<DecoratedObject> */
    public function getDecoratorFQN(): string {
        return match($this) {
            TypeNameValue::ANNOT => Annot::class,
            TypeNameValue::CATALOG => Catalog::class,
            TypeNameValue::ENCODING => Encoding::class,
            TypeNameValue::EXT_G_STATE => ExtGState::class,
            TypeNameValue::FONT => Font::class,
            TypeNameValue::FONT_DESCRIPTOR => FontDescriptor::class,
            TypeNameValue::GROUP => Group::class,
            TypeNameValue::MARK_INFO => MarkInfo::class,
            TypeNameValue::METADATA => MetaData::class,
            TypeNameValue::OBJ_STM => ObjectStream::class,
            TypeNameValue::OUTLINES => Outlines::class,
            TypeNameValue::PAGE => Page::class,
            TypeNameValue::PAGES => Pages::class,
            TypeNameValue::STREAM => StreamObject::class,
            TypeNameValue::VIEWER_PREFERENCES => ViewerPreferences::class,
            TypeNameValue::X_OBJECT => XObject::class,
            TypeNameValue::X_REF => XRef::class,
        };
    }
}
