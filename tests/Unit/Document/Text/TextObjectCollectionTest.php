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

#[CoversClass(TextObjectCollection::class)]
class TextObjectCollectionTest extends TestCase {
    public function testToString(): void {
        static::assertEquals(
            'Hello World',
            (string) (new TextObjectCollection(
                (new TextObject())
                    ->addTextOperator(new TextOperator(TextStateOperator::FONT_SIZE, '/F1 24'))
                    ->addTextOperator(new TextOperator(TextPositioningOperator::MOVE_OFFSET, '100 100'))
                    ->addTextOperator(new TextOperator(TextShowingOperator::SHOW, '( Hello World )'))
            ))
        );
    }

    public function testToStringWithShowArray(): void {
        static::assertEquals(
            'Hello World',
            (string) (new TextObjectCollection(
                (new TextObject())
                    ->addTextOperator(new TextOperator(TextStateOperator::FONT_SIZE, '/F1 24'))
                    ->addTextOperator(new TextOperator(TextPositioningOperator::MOVE_OFFSET, '100 100'))
                    ->addTextOperator(new TextOperator(TextShowingOperator::SHOW_ARRAY, '[(Hello)30( World)]'))
            ))
        );
    }
}
