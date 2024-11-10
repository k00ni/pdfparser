<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Array\ArrayValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Array\WValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Boolean\BooleanValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Date\DateValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\DictionaryValueType;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Float\FloatValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Integer\IntegerValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\PageModeNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\SubtypeNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\TrappedNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Rectangle\Rectangle;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Reference\ReferenceValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\FilterNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Reference\ReferenceValueArray;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\TextString\TextStringValue;
use PrinsFrank\PdfParser\Exception\ParseFailureException;
use Throwable;

class DictionaryValue {
    /** @throws ParseFailureException */
    public static function fromValueString(DictionaryKey $dictionaryKey, string $valueString): TrappedNameValue|DictionaryValueType|TypeNameValue|SubtypeNameValue|FilterNameValue {
        $isLiteralString = str_starts_with($valueString, '(') && str_ends_with($valueString, ')');
        try {
            return match ($dictionaryKey) {
                DictionaryKey::FILTER => FilterNameValue::fromValue($valueString),
                DictionaryKey::TYPE => TypeNameValue::fromValue($valueString),
                DictionaryKey::TRAPPED => TrappedNameValue::fromValue($valueString),
                DictionaryKey::INDEX,
                DictionaryKey::ID,
                DictionaryKey::CROP_BOX,
                DictionaryKey::B_BOX,
                DictionaryKey::MATRIX,
                DictionaryKey::RECT,
                DictionaryKey::BORDER,
                DictionaryKey::ARTBOX => ArrayValue::fromValue($valueString),
                DictionaryKey::W => WValue::fromValue($valueString),
                DictionaryKey::COLUMNS,
                DictionaryKey::PREDICTOR,
                DictionaryKey::PREVIOUS,
                DictionaryKey::N,
                DictionaryKey::FIRST,
                DictionaryKey::FIRST_CHAR,
                DictionaryKey::FLAGS,
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
                DictionaryKey::LENGTH,
                DictionaryKey::SIZE => IntegerValue::fromValue($valueString),
                DictionaryKey::ASCENT => FloatValue::fromValue($valueString),
                DictionaryKey::INFO,
                DictionaryKey::METADATA,
                DictionaryKey::PAGES,
                DictionaryKey::STRUCT_TREE_ROOT,
                DictionaryKey::ANNOTS,
                DictionaryKey::S_MASK,
                DictionaryKey::HELV,
                DictionaryKey::OUTLINES,
                DictionaryKey::TO_UNICODE,
                DictionaryKey::PAGE_LABELS,
                DictionaryKey::C_I_D_SET,
                DictionaryKey::DESCENDANT_FONTS,
                DictionaryKey::PARENT,
                DictionaryKey::PROCSET,
                DictionaryKey::F,
                DictionaryKey::ROOT => ReferenceValue::fromValue($valueString),
                DictionaryKey::CONTENTS => $isLiteralString ? TextStringValue::fromValue($valueString) : ReferenceValueArray::fromValue($valueString),
                DictionaryKey::RESOURCES,
                DictionaryKey::KIDS => ReferenceValueArray::fromValue($valueString),
                DictionaryKey::CREATOR,
                DictionaryKey::OPEN_ACTION,
                DictionaryKey::GS,
                DictionaryKey::BITS_PER_COMPONENT,
                DictionaryKey::PTEX_FULL_BANNER,
                DictionaryKey::FONT,
                DictionaryKey::PDF,
                DictionaryKey::FONT_NAME,
                DictionaryKey::CHAR_SET,
                DictionaryKey::BASE_FONT,
                DictionaryKey::FONT_DESCRIPTOR,
                DictionaryKey::ENCRYPTION,
                DictionaryKey::DOC_CHECKSUM,
                DictionaryKey::X_REF_STM,
                DictionaryKey::C,
                DictionaryKey::I,
                DictionaryKey::S,
                DictionaryKey::V,
                DictionaryKey::ACRO_FORM,
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
                DictionaryKey::X_OBJECT,
                DictionaryKey::FONT_FILE,
                DictionaryKey::CA,
                DictionaryKey::CA_L,
                DictionaryKey::C_S,
                DictionaryKey::W_M,
                DictionaryKey::G_S,
                DictionaryKey::TC,
                DictionaryKey::O_P_M,
                DictionaryKey::INTENT,
                DictionaryKey::PAGE_LAYOUT,
                DictionaryKey::REGISTRY,
                DictionaryKey::ORDERING,
                DictionaryKey::SUPPLEMENT,
                DictionaryKey::C_I_D_TO_G_I_D_MAP,
                DictionaryKey::A_I_S,
                DictionaryKey::FONT_FAMILY,
                DictionaryKey::C_I_D_SYSTEM_INFO,
                DictionaryKey::DIRECTION,
                DictionaryKey::D_W,
                DictionaryKey::FONT_STRETCH,
                DictionaryKey::B_M,
                DictionaryKey::BLEED_BOX,
                DictionaryKey::FONT_WEIGHT,
                DictionaryKey::IN_DESIGN,
                DictionaryKey::O_P,
                DictionaryKey::IMAGE,
                DictionaryKey::T_P_L,
                DictionaryKey::S_A,
                DictionaryKey::S_M,
                DictionaryKey::C_O_,
                DictionaryKey::PAGE,
                DictionaryKey::PRODUCER => TextStringValue::fromValue($valueString),
                DictionaryKey::MOD_DATE,
                DictionaryKey::CREATION_DATE => DateValue::fromValue($valueString),
                DictionaryKey::FONT_B_BOX,
                DictionaryKey::MEDIABOX => Rectangle::fromValue($valueString),
                DictionaryKey::SUBTYPE => SubtypeNameValue::fromValue($valueString),
                DictionaryKey::PAGE_MODE => PageModeNameValue::fromValue($valueString),
                DictionaryKey::OPEN => BooleanValue::fromValue($valueString),
                default => throw new ParseFailureException('Dictionary key "' . $dictionaryKey->name . '" is not supported (' . $valueString . ')'),
            };
        } catch (Throwable $e) {
            throw new ParseFailureException($e->getMessage() . ' for dictionary key of type "' . $dictionaryKey->value . '"');
        }
    }

    /** @throws ParseFailureException */
    public static function fromValueArray(DictionaryKey $dictionaryKey, array $valueArray): TrappedNameValue|DictionaryValueType|TypeNameValue|SubtypeNameValue|FilterNameValue {
        try {
            return match ($dictionaryKey) {
                DictionaryKey::INDEX,
                DictionaryKey::ID,
                DictionaryKey::CROP_BOX,
                DictionaryKey::B_BOX,
                DictionaryKey::MATRIX,
                DictionaryKey::RECT,
                DictionaryKey::BORDER,
                DictionaryKey::DECODE_PARAMS,
                DictionaryKey::RESOURCES,
                DictionaryKey::FONT,
                DictionaryKey::ARTBOX => new ArrayValue($valueArray),
                DictionaryKey::W => new WValue($valueArray),
                default => throw new ParseFailureException('Dictionary key "' . $dictionaryKey->name . '" is not supported.'),
            };
        } catch (Throwable $e) {
            throw new ParseFailureException($e->getMessage() . ' for dictionary key of type "' . $dictionaryKey->value . '"');
        }
    }
}
