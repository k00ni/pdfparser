<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Tests\Unit\Document\CrossReference\Source\Section\SubSection\Entry;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PrinsFrank\PdfParser\Document\CrossReference\Source\Section\SubSection\Entry\CrossReferenceEntryCompressed;

#[CoversClass(CrossReferenceEntryCompressed::class)]
class CrossReferenceEntryCompressedTest extends TestCase {
    public function testConstruct(): void {
        $entry = new CrossReferenceEntryCompressed(42, 43);
        static::assertSame(42, $entry->storedInStreamWithObjectNumber);
        static::assertSame(43, $entry->indexOfThisObjectWithinObjectStream);
    }
}
