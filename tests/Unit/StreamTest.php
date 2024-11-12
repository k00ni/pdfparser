<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PrinsFrank\PdfParser\Document\Generic\Marker;
use PrinsFrank\PdfParser\Stream;

#[CoversClass(Stream::class)]
class StreamTest extends TestCase {
    public function testStrrpos(): void {
        $stream = Stream::fromString('123objxref');
        static::assertSame(
            3,
            $stream->lastPos(Marker::OBJ, 0)
        );
        static::assertSame(
            6,
            $stream->lastPos(Marker::XREF, 0)
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
        $eofMarkerPos = $stream->lastPos(Marker::EOF, 0);
        static::assertNotNull($eofMarkerPos);
        static::assertSame(Marker::EOF->value, $stream->read($eofMarkerPos, strlen(Marker::EOF->value)));

        $startXrefPos = $stream->lastPos(Marker::START_XREF, $stream->getSizeInBytes() - $eofMarkerPos);
        static::assertNotNull($startXrefPos);
        static::assertSame(Marker::START_XREF->value, $stream->read($startXrefPos, strlen(Marker::START_XREF->value)));

        $byteOffsetPos = $stream->getStartOfNextLine($startXrefPos, $stream->getSizeInBytes());
        static::assertNotNull($byteOffsetPos);
        $byteOffsetEndPos = $stream->getEndOfCurrentLine($byteOffsetPos, $stream->getSizeInBytes());
        static::assertNotNull($byteOffsetEndPos);
        static::assertSame('1234', $stream->read($byteOffsetPos, $byteOffsetEndPos - $byteOffsetPos));
    }
}
