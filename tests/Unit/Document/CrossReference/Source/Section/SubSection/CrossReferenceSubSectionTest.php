<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Tests\Unit\Document\CrossReference\Source\Section\SubSection;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PrinsFrank\PdfParser\Document\CrossReference\Source\Section\SubSection\CrossReferenceSubSection;
use PrinsFrank\PdfParser\Exception\InvalidArgumentException;

#[CoversClass(CrossReferenceSubSection::class)]
class CrossReferenceSubSectionTest extends TestCase {
    public function testConstructThrowsExceptionWithNegativeNumberOfEntries(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$nrOfEntries should be a positive number');
        new CrossReferenceSubSection(42, -1);
    }

    public function testContainsObject(): void {
        $subSection = new CrossReferenceSubSection(0, 0);
        static::assertFalse($subSection->containsObject(0));
        static::assertFalse($subSection->containsObject(1));

        $subSection = new CrossReferenceSubSection(0, 1);
        static::assertTrue($subSection->containsObject(0));
        static::assertFalse($subSection->containsObject(1));

        $subSection = new CrossReferenceSubSection(42, 1);
        static::assertTrue($subSection->containsObject(42));
        static::assertFalse($subSection->containsObject(43));
    }
}
