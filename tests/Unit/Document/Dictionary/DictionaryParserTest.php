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
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Array\WValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Date\DateValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Float\FloatValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Integer\IntegerValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\FilterNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\PageModeNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\TrappedNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Rectangle\Rectangle;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Reference\ReferenceValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Reference\ReferenceValueArray;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\TextString\TextStringValue;
use PrinsFrank\PdfParser\Stream;

#[CoversClass(DictionaryParser::class)]
class DictionaryParserTest extends TestCase {
    public function testParseCrossReference(): void {
        static::assertEquals(
            new Dictionary(
                new DictionaryEntry(DictionaryKey::TYPE, TypeNameValue::X_REF),
                new DictionaryEntry(DictionaryKey::INDEX, new ArrayValue([0, 16])),
                new DictionaryEntry(DictionaryKey::SIZE, new IntegerValue(16)),
                new DictionaryEntry(DictionaryKey::W, new WValue(1, 2, 1)),
                new DictionaryEntry(DictionaryKey::ROOT, new ReferenceValue(13, 0)),
                new DictionaryEntry(DictionaryKey::INFO, new ReferenceValue(14, 0)),
                new DictionaryEntry(DictionaryKey::ID, new ArrayValue(['<F7F55EED423E47B1F3E311DE7CFCE2E5>', '<F7F55EED423E47B1F3E311DE7CFCE2E5>'])),
                new DictionaryEntry(DictionaryKey::LENGTH, new IntegerValue(57)),
                new DictionaryEntry(DictionaryKey::FILTER, FilterNameValue::FLATE_DECODE),
            ),
            DictionaryParser::parse(
                $stream = Stream::fromString(
                    <<<EOD
                    15 0 obj
                    <<
                    /Type /XRef
                    /Index [0 16]
                    /Size 16
                    /W [1 2 1]
                    /Root 13 0 R
                    /Info 14 0 R
                    /ID [<F7F55EED423E47B1F3E311DE7CFCE2E5> <F7F55EED423E47B1F3E311DE7CFCE2E5>]
                    /Length 57
                    /Filter /FlateDecode
                    >>
                    stream
                    EOD,
                ),
                0,
                $stream->getSizeInBytes(),
            )
        );
    }

    public function testParseCrossReferencePaddedArrayValues(): void {
        static::assertEquals(
            new Dictionary(
                new DictionaryEntry(DictionaryKey::INDEX, new ArrayValue([0, 16])),
            ),
            DictionaryParser::parse(
                $stream = Stream::fromString(
                    <<<EOD
                    <<
                    /Index [ 0 16 ]
                    >>
                    EOD,
                ),
                0,
                $stream->getSizeInBytes(),
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
                new DictionaryEntry(DictionaryKey::W, new WValue(1, 4, 0)),
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
                new DictionaryEntry(DictionaryKey::W, new WValue(1, 4, 0)),
            ),
            DictionaryParser::parse(
                $stream = Stream::fromString('<</DecodeParms<</Columns 5/Predictor 12>>/Filter/FlateDecode/ID[<9A27A23F6A2546448EBB340FF38477BD><C5C4714E306446ABAE40FE784477D322>]/Index[2460 1 4311 1 4317 2 4414 1 4717 21]/Info 4318 0 R/Length 106/Prev 46153797/Root 4320 0 R/Size 4738/Type/XRef/W[1 4 0]>>stream'),
                0,
                $stream->getSizeInBytes(),
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
                    <<<EOD
                    <<
                    /Type /FontDescriptor
                    /FontName /TAIPAH+CMR10
                    /Flags 4
                    /FontBBox [-40 -250 1009 750]
                    /Ascent 694
                    /CapHeight 683
                    /Descent -194
                    /ItalicAngle 0
                    /StemV 69
                    /XHeight 431
                    /CharSet (/S/a/c/d/e/fi/g/l/n/o/one/p/r/s/t/two)
                    /FontFile 11 0 R
                    >>
                    EOD,
                ),
                0,
                $stream->getSizeInBytes(),
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
                    <<<EOD
                    <<
                    /Producer (pdfTeX-1.40.18)
                    /Creator (TeX)
                    /CreationDate (D:20220506201153+02'00')
                    /ModDate (D:20220506201153+02'00')
                    /Trapped /False
                    /PTEX.Fullbanner (This is pdfTeX, Version 3.14159265-2.6-1.40.18 (TeX Live 2017/Debian) kpathsea version 6.2.3)
                    >>
                    EOD,
                ),
                0,
                $stream->getSizeInBytes(),
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
                    <<<EOD
                    <<
                    /Producer (pdfTeX-1.40.18)
                    %/Creator (TeX)
                    %  /Creator (TeX)
                    >>
                    EOD,
                ),
                0,
                $stream->getSizeInBytes(),
            )
        );
    }

    public function testHandlesNumsNumberTree(): void {
        static::assertEquals(
            new Dictionary(
                new DictionaryEntry(DictionaryKey::OPEN_ACTION, new TextStringValue('[3 0 R/Fit]')),
                new DictionaryEntry(DictionaryKey::PAGE_MODE, PageModeNameValue::USE_OUTLINES),
                new DictionaryEntry(DictionaryKey::PAGE_LABELS, new ArrayValue([
                    new DictionaryEntry(DictionaryKey::NUMS, new TextStringValue('[0<</S/r>>12<</S/D>>]'))
                ])),
                new DictionaryEntry(DictionaryKey::NAMES, new ReferenceValueArray(new ReferenceValue(13164, 0))),
                new DictionaryEntry(DictionaryKey::OUTLINES, new ReferenceValue(13165, 0)),
                new DictionaryEntry(DictionaryKey::PAGES, new ReferenceValue(13221, 0)),
                new DictionaryEntry(DictionaryKey::TYPE, TypeNameValue::CATALOG),
            ),
            DictionaryParser::parse(
                $stream = Stream::fromString(
                    <<<EOD
                    <<
                    /OpenAction[3 0 R/Fit]
                    /PageMode/UseOutlines
                    /PageLabels<</Nums[0<</S/r>>12<</S/D>>]>>
                    /Names 13164 0 R
                    /Outlines 13165 0 R
                    /Pages 13221 0 R
                    /Type/Catalog
                    >>
                    EOD,
                ),
                0,
                $stream->getSizeInBytes(),
            )
        );
    }
}
