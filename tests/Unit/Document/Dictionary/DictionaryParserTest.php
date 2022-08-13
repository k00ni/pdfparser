<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Tests\Unit\Document\Dictionary;

use PHPUnit\Framework\TestCase;
use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryEntry\DictionaryEntry;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParser;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Array\ArrayValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Date\DateValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Integer\IntegerValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\FilterNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\TrappedNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Rectangle\Rectangle;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Reference\ReferenceValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\TextString\TextStringValue;

/**
 * @coversDefaultClass \PrinsFrank\PdfParser\Document\Dictionary\DictionaryParser
 */
class DictionaryParserTest extends TestCase
{
    /**
     * @covers ::parse
     */
    public function testParseCrossReference(): void
    {
        static::assertEquals(
            (new Dictionary())
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::TYPE)->setValue(TypeNameValue::X_REF))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::INDEX)->setValue(new ArrayValue([0, 16])))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::SIZE)->setValue(new IntegerValue(16)))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::W)->setValue(new ArrayValue([1, 2, 1])))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::ROOT)->setValue(new ReferenceValue(13, 0)))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::INFO)->setValue(new ReferenceValue(14, 0)))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::ID)->setValue(new ArrayValue(['<F7F55EED423E47B1F3E311DE7CFCE2E5>', '<F7F55EED423E47B1F3E311DE7CFCE2E5>'])))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::LENGTH)->setValue(new TextStringValue('57')))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::FILTER)->setValue(FilterNameValue::FLATE_DECODE))
            ,
            DictionaryParser::parse(
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
                'stream' . PHP_EOL
            )
        );
    }

    public function testObjectStream(): void
    {
        static::assertEquals(
            (new Dictionary())
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::DECODE_PARAMS)->setValue(new ArrayValue(
                    [
                        (new DictionaryEntry())->setKey(DictionaryKey::COLUMNS)->setValue(new IntegerValue(5)),
                        (new DictionaryEntry())->setKey(DictionaryKey::PREDICTOR)->setValue(new IntegerValue(12))
                    ]
                )))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::FILTER)->setValue(FilterNameValue::FLATE_DECODE))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::ID)->setValue(new ArrayValue(['<9A27A23F6A2546448EBB340FF38477BD>', '<C5C4714E306446ABAE40FE784477D322>'])))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::INDEX)->setValue(new ArrayValue([2460, 1, 4311, 1, 4317, 2, 4414, 1, 4717, 21])))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::INFO)->setValue(new ReferenceValue(4318,0)))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::LENGTH)->setValue(new TextStringValue('106')))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::PREVIOUS)->setValue(new IntegerValue(46153797)))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::ROOT)->setValue(new ReferenceValue(4320, 0)))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::SIZE)->setValue(new IntegerValue(4738)))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::TYPE)->setValue(TypeNameValue::X_REF))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::W)->setValue(new ArrayValue([1, 4, 0])))
            ,
            DictionaryParser::parse(
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
                >>stream')
        );
    }

    /**
     * @covers ::parse
     */
    public function testParseSingleLine(): void
    {
        static::assertEquals(
            (new Dictionary())
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::DECODE_PARAMS)->setValue(new ArrayValue(
                    [
                        (new DictionaryEntry())->setKey(DictionaryKey::COLUMNS)->setValue(new IntegerValue(5)),
                        (new DictionaryEntry())->setKey(DictionaryKey::PREDICTOR)->setValue(new IntegerValue(12))
                    ]
                )))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::FILTER)->setValue(FilterNameValue::FLATE_DECODE))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::ID)->setValue(new ArrayValue(['<9A27A23F6A2546448EBB340FF38477BD>', '<C5C4714E306446ABAE40FE784477D322>'])))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::INDEX)->setValue(new ArrayValue([2460, 1, 4311, 1, 4317, 2, 4414, 1, 4717, 21])))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::INFO)->setValue(new ReferenceValue(4318,0)))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::LENGTH)->setValue(new TextStringValue('106')))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::PREVIOUS)->setValue(new IntegerValue(46153797)))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::ROOT)->setValue(new ReferenceValue(4320, 0)))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::SIZE)->setValue(new IntegerValue(4738)))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::TYPE)->setValue(TypeNameValue::X_REF))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::W)->setValue(new ArrayValue([1, 4, 0])))
            ,
            DictionaryParser::parse('<</DecodeParms<</Columns 5/Predictor 12>>/Filter/FlateDecode/ID[<9A27A23F6A2546448EBB340FF38477BD><C5C4714E306446ABAE40FE784477D322>]/Index[2460 1 4311 1 4317 2 4414 1 4717 21]/Info 4318 0 R/Length 106/Prev 46153797/Root 4320 0 R/Size 4738/Type/XRef/W[1 4 0]>>stream')
        );
    }

    /**
     * @covers ::parse
     */
    public function testParseFontInfo(): void
    {
        static::assertEquals(
            (new Dictionary())
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::TYPE)->setValue(TypeNameValue::FONT_DESCRIPTOR))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::FONT_NAME)->setValue(new TextStringValue('/TAIPAH+CMR10')))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::FLAGS)->setValue(new IntegerValue(4)))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::FONT_B_BOX)->setValue(new Rectangle(-40, -250, 1009, 750)))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::ASCENT)->setValue(new IntegerValue(694)))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::CAP_HEIGHT)->setValue(new IntegerValue(683)))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::DESCENT)->setValue(new IntegerValue(-194)))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::ITALIC_ANGLE)->setValue(new IntegerValue(0)))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::STEM_V)->setValue(new IntegerValue(69)))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::X_HEIGHT)->setValue(new IntegerValue(431)))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::CHAR_SET)->setValue(new TextStringValue('(/S/a/c/d/e/fi/g/l/n/o/one/p/r/s/t/two)')))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::FONT_FILE)->setValue(new TextStringValue('11 0 R')))
            ,
            DictionaryParser::parse(
                '<<' . PHP_EOL .
                '/Type /FontDescriptor' . PHP_EOL .
                '/FontName /TAIPAH+CMR10' . PHP_EOL .
                '/Flags 4'. PHP_EOL .
                '/FontBBox [-40 -250 1009 750]' . PHP_EOL .
                '/Ascent 694' . PHP_EOL .
                '/CapHeight 683' . PHP_EOL .
                '/Descent -194' . PHP_EOL .
                '/ItalicAngle 0' . PHP_EOL .
                '/StemV 69' . PHP_EOL .
                '/XHeight 431' . PHP_EOL .
                '/CharSet (/S/a/c/d/e/fi/g/l/n/o/one/p/r/s/t/two)' . PHP_EOL .
                '/FontFile 11 0 R' . PHP_EOL .
                '>>'
            )
        );
    }

    /**
     * @covers ::parse
     */
    public function testParseValuesEncapsulatedInParentheses(): void
    {
        static::assertEquals(

            (new Dictionary())
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::PRODUCER)->setValue(new TextStringValue('(pdfTeX-1.40.18)')))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::CREATOR)->setValue(new TextStringValue('(TeX)')))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::CREATION_DATE)->setValue(new DateValue('(D:20220506201153+02\'00\')')))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::MOD_DATE)->setValue(new DateValue('(D:20220506201153+02\'00\')')))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::TRAPPED)->setValue(TrappedNameValue::FALSE))
                ->addEntry((new DictionaryEntry())->setKey(DictionaryKey::PTEX_FULL_BANNER)->setValue(new TextStringValue('(This is pdfTeX, Version 3.14159265-2.6-1.40.18 (TeX Live 2017/Debian) kpathsea version 6.2.3)')))
            ,
            DictionaryParser::parse(
                '<<' . PHP_EOL .
                '/Producer (pdfTeX-1.40.18)' . PHP_EOL .
                '/Creator (TeX)' . PHP_EOL .
                '/CreationDate (D:20220506201153+02\'00\')' . PHP_EOL .
                '/ModDate (D:20220506201153+02\'00\')' . PHP_EOL .
                '/Trapped /False' . PHP_EOL .
                '/PTEX.Fullbanner (This is pdfTeX, Version 3.14159265-2.6-1.40.18 (TeX Live 2017/Debian) kpathsea version 6.2.3)' . PHP_EOL .
                '>>'
            )
        );
    }
}
