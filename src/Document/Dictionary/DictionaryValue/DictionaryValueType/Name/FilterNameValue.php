<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name;

use Override;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Array\ArrayValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Integer\IntegerValue;
use PrinsFrank\PdfParser\Document\Filter\Decode\FlateDecode;
use PrinsFrank\PdfParser\Document\Filter\Decode\LZWFlatePredictorValue;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

enum FilterNameValue: string implements NameValue {
    case ASCII_HEX_DECODE = 'ASCIIHexDecode';
    case ASCII_85_DECODE = 'ASCII85Decode';
    case LZW_DECODE = 'LZWDecode';
    case FLATE_DECODE = 'FlateDecode';
    case RUN_LENGTH_DECODE = 'RunLengthDecode';
    case CCITT_FAX_DECODE = 'CCITTFaxDecode';
    case JBIG2_DECODE = 'JBIG2Decode';
    case DCT_DECODE = 'DCTDecode'; // Grayscale or color image data encoded in JPEG baseline format
    case JBX_DECODE = 'JPXDecode';
    case CRYPT = 'Crypt';


    #[Override]
    public static function fromValue(string $valueString): self {
        return self::from(trim(rtrim(ltrim($valueString, '/[')), ']'));
    }

    public function decode(string $content, ?ArrayValue $decodeParams): string {
        return match($this) {
            self::DCT_DECODE => $content, // Dont decode JPEG content
            self::FLATE_DECODE => FlateDecode::decode(
                $content,
                $decodeParams !== null ? LZWFlatePredictorValue::from((int) $decodeParams->getValueForKey(DictionaryKey::PREDICTOR, IntegerValue::class)?->value) : LZWFlatePredictorValue::None,
                $decodeParams?->getValueForKey(DictionaryKey::COLUMNS, IntegerValue::class)?->value
            ),
            default => throw new ParseFailureException('Content "' . $content . '" cannot be decoded for filter "' . $this->name . '"')
        };
    }
}
