<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Tests\Unit\Document\Text;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PrinsFrank\PdfParser\Unused\Text\OperatorString\TextPositioningOperator;
use PrinsFrank\PdfParser\Unused\Text\OperatorString\TextShowingOperator;
use PrinsFrank\PdfParser\Unused\Text\OperatorString\TextStateOperator;
use PrinsFrank\PdfParser\Unused\Text\TextObject;
use PrinsFrank\PdfParser\Unused\Text\TextObjectCollection;
use PrinsFrank\PdfParser\Unused\Text\TextOperator;

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
