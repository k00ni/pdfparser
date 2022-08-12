<?php
declare(strict_types=1);

namespace Document\Generic\Parsing;

use PHPUnit\Framework\TestCase;
use PrinsFrank\PdfParser\Document\Generic\Parsing\CharBuffer;
use PrinsFrank\PdfParser\Exception\BufferTooSmallException;

/**
 * @coversDefaultClass \PrinsFrank\PdfParser\Document\Generic\Parsing\CharBuffer
 */
class CharBufferTest extends TestCase
{
    /**
     * @covers ::setCharacter
     * @covers ::getPreviousCharacter
     * @throws BufferTooSmallException
     */
    public function testGetPreviousCharacter(): void
    {
        $charBuffer = new CharBuffer(3);
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
}
