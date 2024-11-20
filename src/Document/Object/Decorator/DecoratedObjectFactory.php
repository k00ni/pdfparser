<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\Decorator;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Object\Item\ObjectItem;
use PrinsFrank\PdfParser\Exception\ParseFailureException;
use PrinsFrank\PdfParser\Stream;

class DecoratedObjectFactory {
    public static function forItem(?ObjectItem $objectItem, Stream $stream, ?TypeNameValue $expectedType): ?DecoratedObject {
        if ($objectItem === null) {
            return null;
        }

        $typeNameValue = $objectItem->getDictionary($stream)->getValueForKey(DictionaryKey::TYPE, TypeNameValue::class);
        if ($expectedType !== null && $typeNameValue !== null && $expectedType !== $typeNameValue) {
            throw new ParseFailureException(sprintf('Expected object of type %s, got %s', $expectedType->name, $typeNameValue->name));
        }

        return match($expectedType ?? $typeNameValue) {
            TypeNameValue::ANNOT => new Annot($objectItem, $stream),
            TypeNameValue::CATALOG => new Catalog($objectItem, $stream),
            TypeNameValue::ENCODING => new Encoding($objectItem, $stream),
            TypeNameValue::EXT_G_STATE => new ExtGState($objectItem, $stream),
            TypeNameValue::FONT => new Font($objectItem, $stream),
            TypeNameValue::FONT_DESCRIPTOR => new FontDescriptor($objectItem, $stream),
            TypeNameValue::GROUP => new Group($objectItem, $stream),
            TypeNameValue::MARK_INFO => new MarkInfo($objectItem, $stream),
            TypeNameValue::METADATA => new MetaData($objectItem, $stream),
            TypeNameValue::OBJ_STM => new ObjectStream($objectItem, $stream),
            TypeNameValue::OUTLINES => new Outlines($objectItem, $stream),
            TypeNameValue::PAGE => new Page($objectItem, $stream),
            TypeNameValue::PAGES => new Pages($objectItem, $stream),
            TypeNameValue::STREAM => new StreamObject($objectItem, $stream),
            TypeNameValue::X_OBJECT => new XObject($objectItem, $stream),
            TypeNameValue::X_REF => new XRef($objectItem, $stream),
            null => new GenericObject($objectItem, $stream),
        };
    }
}
