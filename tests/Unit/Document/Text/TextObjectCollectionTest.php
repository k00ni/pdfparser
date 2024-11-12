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
    public function testParseText(): void {
        static::assertEquals(
            '[(Sen)28(tence)-334(on)-333(rst)-333(page)][(1)]',
            (string) (new TextObjectCollection())
                ->addTextObject(
                    (new TextObject())
                        ->addTextOperator(new TextOperator(TextStateOperator::FONT_SIZE, '/F20 9.9626'))
                        ->addTextOperator(new TextOperator(TextPositioningOperator::MOVE_OFFSET, '148.712 707.125'))
                        ->addTextOperator(new TextOperator(TextShowingOperator::SHOW_ARRAY, '[(Sen)28(tence)-334(on)-333(rst)-333(page)]'))
                        ->addTextOperator(new TextOperator(TextPositioningOperator::MOVE_OFFSET, '154.421 -567.87'))
                        ->addTextOperator(new TextOperator(TextShowingOperator::SHOW_ARRAY, '[(1)]'))
                ),
        );
    }
}
