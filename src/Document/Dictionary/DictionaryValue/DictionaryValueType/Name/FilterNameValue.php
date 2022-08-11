<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name;

use PrinsFrank\PdfParser\Document\Filter\Decode\FlateDecode;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

enum FilterNameValue: string implements NameValue
{
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

    public static function fromValue(string $valueString): self
    {
        return self::from(trim(ltrim($valueString, '/')));
    }

    /**
     * @throws ParseFailureException
     */
    public static function decode(self $filter, string $content): ?string
    {
        return match($filter) {
            self::DCT_DECODE => $content, // Dont decode JPEG content
            self::FLATE_DECODE => FlateDecode::decode($content),
            default => throw new ParseFailureException('Content "' . $content . '" cannot be decoded for filter "' . $filter->name . '"')
        };
    }
}
