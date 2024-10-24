<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Tests\Feature;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PrinsFrank\PdfParser\File;

#[CoversClass(File::class)]
class FileTest extends TestCase {
    public function testFoo(): void {
        $file = File::open(__DIR__ . '/test.txt');
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
}