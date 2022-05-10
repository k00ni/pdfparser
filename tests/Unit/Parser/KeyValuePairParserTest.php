<?php
declare(strict_types=1);

namespace Parser;

use PHPUnit\Framework\TestCase;
use PrinsFrank\PdfParser\Parser\KeyValuePairParser;

class KeyValuePairParserTest extends TestCase
{
    public function testParseMultiLine(): void
    {
        static::assertSame(
            [
                'Type' => '/XRef',
                'Index' => '[0 16]',
                'Size' => '16',
                'W' => '[1 2 1]',
                'Root' => '13 0 R',
                'Info' => '14 0 R',
                'ID' => '<F7F55EED423E47B1F3E311DE7CFCE2E5> <F7F55EED423E47B1F3E311DE7CFCE2E5>]',
                'Length' => '57',
                'Filter' => '/FlatDecode'
            ],
            KeyValuePairParser::parse(
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

    public function testParseSingleLine(): void
    {
        static::assertSame(
            [
                ''
            ],
            KeyValuePairParser::parse('<</DecodeParms<</Columns 5/Predictor 12>>/Filter/FlateDecode/ID[<9A27A23F6A2546448EBB340FF38477BD><C5C4714E306446ABAE40FE784477D322>]/Index[2460 1 4311 1 4317 2 4414 1 4717 21]/Info 4318 0 R/Length 106/Prev 46153797/Root 4320 0 R/Size 4738/Type/XRef/W[1 4 0]>>stream')
        );
    }
}
