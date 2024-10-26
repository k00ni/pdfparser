<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Tests\Unit\Document\Dictionary;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryEntry\DictionaryEntry;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParser;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Array\ArrayValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Date\DateValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Float\FloatValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Integer\IntegerValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\FilterNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\TrappedNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Rectangle\Rectangle;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Reference\ReferenceValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\TextString\TextStringValue;
use PrinsFrank\PdfParser\Document\Errors\ErrorCollection;
use PrinsFrank\PdfParser\Stream;

#[CoversClass(DictionaryParser::class)]
class DictionaryParserTest extends TestCase {
    public function testParseCrossReference(): void {
        static::assertEquals(
            new Dictionary(
                new DictionaryEntry(DictionaryKey::TYPE, TypeNameValue::X_REF),
                new DictionaryEntry(DictionaryKey::INDEX, new ArrayValue([0, 16])),
                new DictionaryEntry(DictionaryKey::SIZE, new IntegerValue(16)),
                new DictionaryEntry(DictionaryKey::W, new ArrayValue([1, 2, 1])),
                new DictionaryEntry(DictionaryKey::ROOT, new ReferenceValue(13, 0)),
                new DictionaryEntry(DictionaryKey::INFO, new ReferenceValue(14, 0)),
                new DictionaryEntry(DictionaryKey::ID, new ArrayValue(['<F7F55EED423E47B1F3E311DE7CFCE2E5>', '<F7F55EED423E47B1F3E311DE7CFCE2E5>'])),
                new DictionaryEntry(DictionaryKey::LENGTH, new IntegerValue(57)),
                new DictionaryEntry(DictionaryKey::FILTER, FilterNameValue::FLATE_DECODE),
            ),
            DictionaryParser::parse(
                $stream = Stream::fromString(
                    '15 0 obj' . PHP_EOL .
                    '<<' . PHP_EOL .
                    '/Type /XRef' . PHP_EOL .
                    '/Index [0 16]' . PHP_EOL .
                    '/Size 16' . PHP_EOL .
                    '/W [1 2 1]' . PHP_EOL .
                    '/Root 13 0 R' . PHP_EOL .
                    '/Info 14 0 R' . PHP_EOL .
                    '/ID [<F7F55EED423E47B1F3E311DE7CFCE2E5> <F7F55EED423E47B1F3E311DE7CFCE2E5>]' . PHP_EOL .
                    '/Length 57' . PHP_EOL .
                    '/Filter /FlateDecode' . PHP_EOL .
                    '>>' . PHP_EOL .
                    'stream' . PHP_EOL,
                ),
                0,
                $stream->getSizeInBytes(),
                new ErrorCollection(),
            )
        );
    }

    public function testObjectStream(): void {
        static::assertEquals(
            new Dictionary(
                new DictionaryEntry(DictionaryKey::DECODE_PARAMS, new ArrayValue(
                    [
                        new DictionaryEntry(DictionaryKey::COLUMNS, new IntegerValue(5)),
                        new DictionaryEntry(DictionaryKey::PREDICTOR, new IntegerValue(12))
                    ]
                )),
                new DictionaryEntry(DictionaryKey::FILTER, FilterNameValue::FLATE_DECODE),
                new DictionaryEntry(DictionaryKey::ID, new ArrayValue(['<9A27A23F6A2546448EBB340FF38477BD>', '<C5C4714E306446ABAE40FE784477D322>'])),
                new DictionaryEntry(DictionaryKey::INDEX, new ArrayValue([2460, 1, 4311, 1, 4317, 2, 4414, 1, 4717, 21])),
                new DictionaryEntry(DictionaryKey::INFO, new ReferenceValue(4318, 0)),
                new DictionaryEntry(DictionaryKey::LENGTH, new IntegerValue(106)),
                new DictionaryEntry(DictionaryKey::PREVIOUS, new IntegerValue(46153797)),
                new DictionaryEntry(DictionaryKey::ROOT, new ReferenceValue(4320, 0)),
                new DictionaryEntry(DictionaryKey::SIZE, new IntegerValue(4738)),
                new DictionaryEntry(DictionaryKey::TYPE, TypeNameValue::X_REF),
                new DictionaryEntry(DictionaryKey::W, new ArrayValue([1, 4, 0])),
            ),
            DictionaryParser::parse(
                $stream = Stream::fromString(
                    '<<
                        /DecodeParms
                                <<
                                    /Columns 5
                                    /Predictor 12
                                >>
                        /Filter/FlateDecode
                        /ID[<9A27A23F6A2546448EBB340FF38477BD><C5C4714E306446ABAE40FE784477D322>]
                        /Index[2460 1 4311 1 4317 2 4414 1 4717 21]
                        /Info 4318 0 R
                        /Length 106
                        /Prev 46153797
                        /Root 4320 0 R
                        /Size 4738
                        /Type/XRef
                        /W[1 4 0]
                    >>stream',
                ),
                0,
                $stream->getSizeInBytes(),
                new ErrorCollection(),
            )
        );
    }


    public function testParseSingleLine(): void {
        static::assertEquals(
            new Dictionary(
                new DictionaryEntry(DictionaryKey::DECODE_PARAMS, new ArrayValue(
                    [
                        new DictionaryEntry(DictionaryKey::COLUMNS, new IntegerValue(5)),
                        new DictionaryEntry(DictionaryKey::PREDICTOR, new IntegerValue(12))
                    ]
                )),
                new DictionaryEntry(DictionaryKey::FILTER, FilterNameValue::FLATE_DECODE),
                new DictionaryEntry(DictionaryKey::ID, new ArrayValue(['<9A27A23F6A2546448EBB340FF38477BD>', '<C5C4714E306446ABAE40FE784477D322>'])),
                new DictionaryEntry(DictionaryKey::INDEX, new ArrayValue([2460, 1, 4311, 1, 4317, 2, 4414, 1, 4717, 21])),
                new DictionaryEntry(DictionaryKey::INFO, new ReferenceValue(4318, 0)),
                new DictionaryEntry(DictionaryKey::LENGTH, new IntegerValue(106)),
                new DictionaryEntry(DictionaryKey::PREVIOUS, new IntegerValue(46153797)),
                new DictionaryEntry(DictionaryKey::ROOT, new ReferenceValue(4320, 0)),
                new DictionaryEntry(DictionaryKey::SIZE, new IntegerValue(4738)),
                new DictionaryEntry(DictionaryKey::TYPE, TypeNameValue::X_REF),
                new DictionaryEntry(DictionaryKey::W, new ArrayValue([1, 4, 0])),
            ),
            DictionaryParser::parse(
                $stream = Stream::fromString('<</DecodeParms<</Columns 5/Predictor 12>>/Filter/FlateDecode/ID[<9A27A23F6A2546448EBB340FF38477BD><C5C4714E306446ABAE40FE784477D322>]/Index[2460 1 4311 1 4317 2 4414 1 4717 21]/Info 4318 0 R/Length 106/Prev 46153797/Root 4320 0 R/Size 4738/Type/XRef/W[1 4 0]>>stream'),
                0,
                $stream->getSizeInBytes(),
                new ErrorCollection()
            )
        );
    }


    public function testParseFontInfo(): void {
        static::assertEquals(
            new Dictionary(
                new DictionaryEntry(DictionaryKey::TYPE, TypeNameValue::FONT_DESCRIPTOR),
                new DictionaryEntry(DictionaryKey::FONT_NAME, new TextStringValue('/TAIPAH+CMR10')),
                new DictionaryEntry(DictionaryKey::FLAGS, new IntegerValue(4)),
                new DictionaryEntry(DictionaryKey::FONT_B_BOX, new Rectangle(-40, -250, 1009, 750)),
                new DictionaryEntry(DictionaryKey::ASCENT, new FloatValue(694)),
                new DictionaryEntry(DictionaryKey::CAP_HEIGHT, new IntegerValue(683)),
                new DictionaryEntry(DictionaryKey::DESCENT, new IntegerValue(-194)),
                new DictionaryEntry(DictionaryKey::ITALIC_ANGLE, new IntegerValue(0)),
                new DictionaryEntry(DictionaryKey::STEM_V, new IntegerValue(69)),
                new DictionaryEntry(DictionaryKey::X_HEIGHT, new IntegerValue(431)),
                new DictionaryEntry(DictionaryKey::CHAR_SET, new TextStringValue('(/S/a/c/d/e/fi/g/l/n/o/one/p/r/s/t/two)')),
                new DictionaryEntry(DictionaryKey::FONT_FILE, new TextStringValue('11 0 R')),
            ),
            DictionaryParser::parse(
                $stream = Stream::fromString(
                    '<<' . PHP_EOL .
                    '/Type /FontDescriptor' . PHP_EOL .
                    '/FontName /TAIPAH+CMR10' . PHP_EOL .
                    '/Flags 4' . PHP_EOL .
                    '/FontBBox [-40 -250 1009 750]' . PHP_EOL .
                    '/Ascent 694' . PHP_EOL .
                    '/CapHeight 683' . PHP_EOL .
                    '/Descent -194' . PHP_EOL .
                    '/ItalicAngle 0' . PHP_EOL .
                    '/StemV 69' . PHP_EOL .
                    '/XHeight 431' . PHP_EOL .
                    '/CharSet (/S/a/c/d/e/fi/g/l/n/o/one/p/r/s/t/two)' . PHP_EOL .
                    '/FontFile 11 0 R' . PHP_EOL .
                    '>>',
                ),
                0,
                $stream->getSizeInBytes(),
                new ErrorCollection()
            )
        );
    }


    public function testParseValuesEncapsulatedInParentheses(): void {
        static::assertEquals(
            new Dictionary(
                new DictionaryEntry(DictionaryKey::PRODUCER, new TextStringValue('(pdfTeX-1.40.18)')),
                new DictionaryEntry(DictionaryKey::CREATOR, new TextStringValue('(TeX)')),
                new DictionaryEntry(DictionaryKey::CREATION_DATE, new DateValue('(D:20220506201153+02\'00\')')),
                new DictionaryEntry(DictionaryKey::MOD_DATE, new DateValue('(D:20220506201153+02\'00\')')),
                new DictionaryEntry(DictionaryKey::TRAPPED, TrappedNameValue::FALSE),
                new DictionaryEntry(DictionaryKey::PTEX_FULL_BANNER, new TextStringValue('(This is pdfTeX, Version 3.14159265-2.6-1.40.18 (TeX Live 2017/Debian) kpathsea version 6.2.3)')),
            ),
            DictionaryParser::parse(
                $stream = Stream::fromString(
                    '<<' . PHP_EOL .
                    '/Producer (pdfTeX-1.40.18)' . PHP_EOL .
                    '/Creator (TeX)' . PHP_EOL .
                    '/CreationDate (D:20220506201153+02\'00\')' . PHP_EOL .
                    '/ModDate (D:20220506201153+02\'00\')' . PHP_EOL .
                    '/Trapped /False' . PHP_EOL .
                    '/PTEX.Fullbanner (This is pdfTeX, Version 3.14159265-2.6-1.40.18 (TeX Live 2017/Debian) kpathsea version 6.2.3)' . PHP_EOL .
                    '>>',
                ),
                0,
                $stream->getSizeInBytes(),
                new ErrorCollection()
            )
        );
    }

    public function testIgnoreCommentedLines(): void {
        static::assertEquals(
            new Dictionary(
                new DictionaryEntry(DictionaryKey::PRODUCER, new TextStringValue('(pdfTeX-1.40.18)')),
            ),
            DictionaryParser::parse(
                $stream = Stream::fromString(
                    '<<' . PHP_EOL .
                    '/Producer (pdfTeX-1.40.18)' . PHP_EOL .
                    '%/Creator (TeX)' . PHP_EOL .
                    '%  /Creator (TeX)' . PHP_EOL .
                    '>>',
                ),
                0,
                $stream->getSizeInBytes(),
                new ErrorCollection()
            )
        );
    }
}
