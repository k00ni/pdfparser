<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Tests\Unit\Document\Text;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PrinsFrank\PdfParser\Document\Text\OperatorString\TextPositioningOperator;
use PrinsFrank\PdfParser\Document\Text\OperatorString\TextShowingOperator;
use PrinsFrank\PdfParser\Document\Text\OperatorString\TextStateOperator;
use PrinsFrank\PdfParser\Document\Text\TextObject;
use PrinsFrank\PdfParser\Document\Text\TextObjectCollection;
use PrinsFrank\PdfParser\Document\Text\TextOperator;
use PrinsFrank\PdfParser\Document\Text\TextParser;

#[CoversClass(TextParser::class)]
class TextParserTest extends TestCase {
    public function testParseText(): void {
        static::assertEquals(
            (new TextObjectCollection())
                ->addTextObject(
                    (new TextObject())
                        ->addTextOperator(new TextOperator(TextStateOperator::FONT_SIZE, '/F20 9.9626'))
                        ->addTextOperator(new TextOperator(TextPositioningOperator::MOVE_OFFSET, '148.712 707.125'))
                        ->addTextOperator(new TextOperator(TextShowingOperator::SHOW_ARRAY, '[(Sen)28(tence)-334(on)-333(rst)-333(page)]'))
                        ->addTextOperator(new TextOperator(TextPositioningOperator::MOVE_OFFSET, '154.421 -567.87'))
                        ->addTextOperator(new TextOperator(TextShowingOperator::SHOW_ARRAY, '[(1)]'))
                ),
            TextParser::parse("BT\n/F20 9.9626 Tf 148.712 707.125 Td [(Sen)28(tence)-334(on)-333(\014rst)-333(page)]TJ 154.421 -567.87 Td [(1)]TJ\nET")
        );
    }
}
