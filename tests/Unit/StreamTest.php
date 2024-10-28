<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PrinsFrank\PdfParser\Document\Generic\Marker;
use PrinsFrank\PdfParser\Stream;

#[CoversClass(Stream::class)]
class StreamTest extends TestCase {
    public function testStrrpos(): void {
        $stream = Stream::fromString('123abc123');
        static::assertSame(
            3,
            $stream->strrpos('abc', 0)
        );
        static::assertSame(
            6,
            $stream->strrpos('123', 0)
        );
        static::assertSame(
            7,
            $stream->strrpos('23', 0)
        );
        static::assertSame(
            8,
            $stream->strrpos('3', 0)
        );
    }

    public function testFileStructure(): void {
        $stream = Stream::fromString(
            <<<EOD
            trailer
            startxref
            1234
            %%EOF
            EOD
        );
        $eofMarkerPos = $stream->strrpos(Marker::EOF->value, 0);
        static::assertNotNull($eofMarkerPos);
        static::assertSame(Marker::EOF->value, $stream->read($eofMarkerPos, strlen(Marker::EOF->value)));

        $startXrefPos = $stream->strrpos(Marker::START_XREF->value, $stream->getSizeInBytes() - $eofMarkerPos);
        static::assertNotNull($startXrefPos);
        static::assertSame(Marker::START_XREF->value, $stream->read($startXrefPos, strlen(Marker::START_XREF->value)));

        $byteOffsetPos = $stream->getStartOfNextLine($startXrefPos);
        static::assertNotNull($byteOffsetPos);
        $byteOffsetEndPos = $stream->getEndOfCurrentLine($byteOffsetPos);
        static::assertNotNull($byteOffsetEndPos);
        static::assertSame('1234', $stream->read($byteOffsetPos, $byteOffsetEndPos - $byteOffsetPos));
    }
}
