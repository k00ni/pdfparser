<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Array\ArrayValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Date\DateValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Dictionary\DictionaryValueValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Integer\IntegerValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\SubtypeNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\TrappedNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Rectangle\Rectangle;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Reference\ReferenceValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\FilterNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\TextString\TextStringValue;
use PrinsFrank\PdfParser\Exception\InvalidDictionaryValueTypeFormatException;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

class DictionaryValue
{
    /**
     * @throws ParseFailureException
     */
    public static function fromValueString(DictionaryKey $dictionaryKey, string $valueString)
    {
        try {
            return match ($dictionaryKey) {
                DictionaryKey::FILTER        => FilterNameValue::fromValue($valueString),
                DictionaryKey::TYPE          => TypeNameValue::fromValue($valueString),
                DictionaryKey::TRAPPED       => TrappedNameValue::fromValue($valueString),
                DictionaryKey::INDEX,
                DictionaryKey::ID,
                DictionaryKey::KIDS,
                DictionaryKey::CROP_BOX,
                DictionaryKey::B_BOX,
                DictionaryKey::MATRIX,
                DictionaryKey::RECT,
                DictionaryKey::BORDER,
                DictionaryKey::W             => ArrayValue::fromValue($valueString),
                DictionaryKey::COLUMNS,
                DictionaryKey::PREDICTOR,
                DictionaryKey::PREVIOUS,
                DictionaryKey::N,
                DictionaryKey::FIRST,
                DictionaryKey::FIRST_CHAR,
                DictionaryKey::FLAGS,
                DictionaryKey::ASCENT,
                DictionaryKey::CAP_HEIGHT,
                DictionaryKey::DESCENT,
                DictionaryKey::ITALIC_ANGLE,
                DictionaryKey::STEM_V,
                DictionaryKey::X_HEIGHT,
                DictionaryKey::COUNT,
                DictionaryKey::LAST_CHAR,
                DictionaryKey::WIDTH,
                DictionaryKey::HEIGHT,
                DictionaryKey::COLORS,
                DictionaryKey::ROTATE,
                DictionaryKey::STRUCT_PARENTS,
                DictionaryKey::FORM_TYPE,
                DictionaryKey::LINEARIZED,
                DictionaryKey::MISSING_WIDTH,
                DictionaryKey::STEM_H,
                DictionaryKey::LEADING,
                DictionaryKey::L,
                DictionaryKey::O,
                DictionaryKey::E,
                DictionaryKey::T,
                DictionaryKey::MAX_WIDTH,
                DictionaryKey::AVG_WIDTH,
                DictionaryKey::SIZE          => IntegerValue::fromValue($valueString),
                DictionaryKey::INFO,
                DictionaryKey::METADATA,
                DictionaryKey::PAGES,
                DictionaryKey::STRUCT_TREE_ROOT,
                DictionaryKey::ANNOTS,
                DictionaryKey::S_MASK,
                DictionaryKey::HELV,
                DictionaryKey::OUTLINES,
                DictionaryKey::TO_UNICODE,
                DictionaryKey::OPEN_ACTION,
                DictionaryKey::ROOT          => ReferenceValue::fromValue($valueString),
                DictionaryKey::CREATOR,
                DictionaryKey::GS,
                DictionaryKey::BITS_PER_COMPONENT,
                DictionaryKey::PTEX_FULL_BANNER,
                DictionaryKey::CONTENTS,
                DictionaryKey::F,
                DictionaryKey::FONT,
                DictionaryKey::PROCSET,
                DictionaryKey::PDF,
                DictionaryKey::FONT_NAME,
                DictionaryKey::CHAR_SET,
                DictionaryKey::BASE_FONT,
                DictionaryKey::FONT_DESCRIPTOR,
                DictionaryKey::FONT_FILE,
                DictionaryKey::ENCRYPTION,
                DictionaryKey::DOC_CHECKSUM,
                DictionaryKey::X_REF_STM,
                DictionaryKey::C,
                DictionaryKey::I,
                DictionaryKey::S,
                DictionaryKey::V,
                DictionaryKey::ACRO_FORM,
                DictionaryKey::LENGTH,
                DictionaryKey::LANG,
                DictionaryKey::COLOR_SPACE,
                DictionaryKey::MARKED,
                DictionaryKey::CS,
                DictionaryKey::TEXT,
                DictionaryKey::IMAGE_C,
                DictionaryKey::TABS,
                DictionaryKey::R,
                DictionaryKey::BASE_ENCODING,
                DictionaryKey::WIDTHS,
                DictionaryKey::NAME,
                DictionaryKey::ENCODING,
                DictionaryKey::TITLE,
                DictionaryKey::AUTHOR,
                DictionaryKey::IM,
                DictionaryKey::DIFFERENCES,
                DictionaryKey::KEYWORDS,
                DictionaryKey::IMAGE_B,
                DictionaryKey::IMAGE_I,
                DictionaryKey::SUBJECT,
                DictionaryKey::H,
                DictionaryKey::URI,
                DictionaryKey::K,
                DictionaryKey::PRODUCER      => TextStringValue::fromValue($valueString),
                DictionaryKey::MOD_DATE,
                DictionaryKey::CREATION_DATE => DateValue::fromValue($valueString),
                DictionaryKey::PARENT,
                DictionaryKey::RESOURCES     => DictionaryValueValue::fromValue($valueString),
                DictionaryKey::FONT_B_BOX,
                DictionaryKey::MEDIABOX      => Rectangle::fromValue($valueString),
                DictionaryKey::SUBTYPE       => SubtypeNameValue::fromValue($valueString),
                default                      => throw new ParseFailureException('Dictionary key "' . $dictionaryKey->name . '" is not supported (' . $valueString . ')'),
            };
        } catch (InvalidDictionaryValueTypeFormatException $e) {
            throw new ParseFailureException($e->getMessage() . ' for dictionary key of type "' . $dictionaryKey->value . '"');
        }
    }
}
