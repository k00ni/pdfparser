<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Tests\Unit\Parser;

use PHPUnit\Framework\TestCase;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParser;

/**
 * @coversDefaultClass \PrinsFrank\PdfParser\Document\Dictionary\DictionaryParser
 */
class DictionaryParserTest extends TestCase
{
    /**
     * @covers ::parse
     */
    public function testParseMultiLine(): void
    {
        static::assertSame(
            [
                '/Type' => '/XRef',
                '/Index' => '[0 16]',
                '/Size' => '16',
                '/W' => '[1 2 1]',
                '/Root' => '13 0 R',
                '/Info' => '14 0 R',
                '/ID' => '[<F7F55EED423E47B1F3E311DE7CFCE2E5> <F7F55EED423E47B1F3E311DE7CFCE2E5>]',
                '/Length' => '57',
                '/Filter' => '/FlateDecode'
            ],
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

    /**
     * @covers ::parse
     */
    public function testParseSingleLine(): void
    {
        static::assertSame(
            [
                '/DecodeParms' => [
                    '/Columns' => '5',
                    '/Predictor' => '12',
                ],
                '/Filter' => '/FlateDecode',
                '/ID' => '[<9A27A23F6A2546448EBB340FF38477BD><C5C4714E306446ABAE40FE784477D322>]',
                '/Index' => '[2460 1 4311 1 4317 2 4414 1 4717 21]',
                '/Info' => '4318 0 R',
                '/Length' => '106',
                '/Prev' => '46153797',
                '/Root' => '4320 0 R',
                '/Size' => '4738',
                '/Type' => '/XRef',
                '/W' => '[1 4 0]',
            ],
            DictionaryParser::parse('<</DecodeParms<</Columns 5/Predictor 12>>/Filter/FlateDecode/ID[<9A27A23F6A2546448EBB340FF38477BD><C5C4714E306446ABAE40FE784477D322>]/Index[2460 1 4311 1 4317 2 4414 1 4717 21]/Info 4318 0 R/Length 106/Prev 46153797/Root 4320 0 R/Size 4738/Type/XRef/W[1 4 0]>>stream')
        );
    }

    /**
     * @covers ::parse
     */
    public function testParseNestedValues(): void
    {
        static::assertSame(
            [
                '/Type' => '/XRef',
                '/Index' => '[0 16]',
                '/Size' => '16',
                '/W' => '[1 2 1]',
                '/Root' => '13 0 R',
                '/Info' => '14 0 R',
                '/ID' => '[<F7F55EED423E47B1F3E311DE7CFCE2E5> <F7F55EED423E47B1F3E311DE7CFCE2E5>]',
                '/Length' => '57',
                '/Filter' => '/FlateDecode'
            ],
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
}
