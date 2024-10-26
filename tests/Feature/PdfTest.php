<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Tests\Feature;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PrinsFrank\PdfParser\Document\Generic\Marker;
use PrinsFrank\PdfParser\Pdf;

#[CoversClass(Pdf::class)]
class PdfTest extends TestCase {
    public function testStrrpos(): void {
        $file = Pdf::open(__DIR__ . '/fixtures/test.txt');
        static::assertSame(
            3,
            $file->strrpos('abc', 0)
        );
        static::assertSame(
            6,
            $file->strrpos('123', 0)
        );
        static::assertSame(
            7,
            $file->strrpos('23', 0)
        );
        static::assertSame(
            8,
            $file->strrpos('3', 0)
        );
    }

    public function testFileStructure(): void {
        $file = Pdf::open(__DIR__ . '/fixtures/file_structure.txt');
        $eofMarkerPos = $file->strrpos(Marker::EOF->value, 0);
        static::assertNotNull($eofMarkerPos);
        static::assertSame(Marker::EOF->value, $file->read($eofMarkerPos, strlen(Marker::EOF->value)));

        $startXrefPos = $file->strrpos(Marker::START_XREF->value, $file->getSizeInBytes() - $eofMarkerPos);
        static::assertNotNull($startXrefPos);
        static::assertSame(Marker::START_XREF->value, $file->read($startXrefPos, strlen(Marker::START_XREF->value)));

        $byteOffsetPos = $file->getStartOfNextLine($startXrefPos);
        static::assertNotNull($byteOffsetPos);
        $byteOffsetEndPos = $file->getEndOfCurrentLine($byteOffsetPos);
        static::assertNotNull($byteOffsetEndPos);
        static::assertSame('1234', $file->read($byteOffsetPos, $byteOffsetEndPos - $byteOffsetPos));
    }
}