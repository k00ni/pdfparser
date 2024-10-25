<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Tests\Unit\Document\Generic\Parsing;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PrinsFrank\PdfParser\Document\Generic\Marker;
use PrinsFrank\PdfParser\Document\Generic\Parsing\RollingCharBuffer;
use PrinsFrank\PdfParser\Exception\BufferTooSmallException;

#[CoversClass(RollingCharBuffer::class)]
class RollingCharBufferTest extends TestCase {
    /**
     * @throws BufferTooSmallException
     */
    public function testGetPreviousCharacter(): void {
        $charBuffer = new RollingCharBuffer(3);
        $charBuffer->setCharacter('a');
        static::assertNull($charBuffer->getPreviousCharacter());
        static::assertNull($charBuffer->getPreviousCharacter(1));
        static::assertNull($charBuffer->getPreviousCharacter(2));

        $charBuffer->next()->setCharacter('b');
        static::assertSame('a', $charBuffer->getPreviousCharacter());
        static::assertSame('a', $charBuffer->getPreviousCharacter(1));
        static::assertNull($charBuffer->getPreviousCharacter(2));

        $charBuffer->next()->setCharacter('c');
        static::assertSame('b', $charBuffer->getPreviousCharacter());
        static::assertSame('b', $charBuffer->getPreviousCharacter(1));
        static::assertSame('a', $charBuffer->getPreviousCharacter(2));

        $charBuffer->next()->next()->setCharacter('d');
        static::assertNull($charBuffer->getPreviousCharacter());
        static::assertNull($charBuffer->getPreviousCharacter(1));
        static::assertSame('c', $charBuffer->getPreviousCharacter(2));
    }

    public function testSeenMarker(): void {
        $charBuffer = new RollingCharBuffer(6);
        static::assertFalse($charBuffer->seenBackedEnumValue(Marker::STREAM));
        $charBuffer->next()->setCharacter('s');
        static::assertFalse($charBuffer->seenBackedEnumValue(Marker::STREAM));
        $charBuffer->next()->setCharacter('t');
        static::assertFalse($charBuffer->seenBackedEnumValue(Marker::STREAM));
        $charBuffer->next()->setCharacter('r');
        static::assertFalse($charBuffer->seenBackedEnumValue(Marker::STREAM));
        $charBuffer->next()->setCharacter('e');
        static::assertFalse($charBuffer->seenBackedEnumValue(Marker::STREAM));
        $charBuffer->next()->setCharacter('a');
        static::assertFalse($charBuffer->seenBackedEnumValue(Marker::STREAM));
        $charBuffer->next()->setCharacter('m');
        static::assertTrue($charBuffer->seenBackedEnumValue(Marker::STREAM));
    }
}
