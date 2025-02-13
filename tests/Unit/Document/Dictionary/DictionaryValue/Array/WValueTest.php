<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Tests\Unit\Document\Dictionary\DictionaryValue\Array;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Array\WValue;
use PrinsFrank\PdfParser\Exception\RuntimeException;

#[CoversClass(WValue::class)]
class WValueTest extends TestCase {
    public function testFromValue(): void {
        static::assertNull(WValue::fromValue(''));
        static::assertNull(WValue::fromValue('[]'));
        static::assertNull(WValue::fromValue('[0]'));
        static::assertNull(WValue::fromValue('[0 1]'));
        static::assertEquals(
            new WValue(0, 1, 2),
            WValue::fromValue('[0 1 2]')
        );
        static::assertEquals(
            new WValue(0, 1, 2),
            WValue::fromValue('[ 0 1 2 ]')
        );
    }

    public function testGetTotalLengthInBytesThrowsExceptionWhenNegative(): void {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Total length should not be less than 1, got -1');
        (new WValue(-1, 0, 0))
            ->getTotalLengthInBytes();
    }

    public function testGetTotalLengthInBytes(): void {
        static::assertSame(
            6,
            (new WValue(1, 2, 3))
                ->getTotalLengthInBytes()
        );
    }
}
