<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Tests\Unit\Document\CrossReference\CrossReferenceTable;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable\CrossReferenceSubSection;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable\CrossReferenceTable;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable\CrossReferenceTableParser;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable\Entry\CrossReferenceEntryFreeObject;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable\Entry\CrossReferenceEntryInUseObject;
use PrinsFrank\PdfParser\Exception\InvalidArgumentException;
use PrinsFrank\PdfParser\Stream;

#[CoversClass(CrossReferenceTableParser::class)]
class CrossReferenceTableParserTest extends TestCase {
    /**
     * 7.5.4 Example 2
     * The following line introduces a subsection containing five objects numbered consecutively from 28 to 32.
     */
    public function testParseExample1(): void {
        $stream = Stream::fromString(
            <<<EOD
            xref
            28 5
            EOD
        );
        static::expectException(InvalidArgumentException::class);
        static::expectExceptionMessage('Cross reference subsection defines 5 entries, got 0');
        CrossReferenceTableParser::parse($stream, 0, $stream->getSizeInBytes());
    }

    /**
     * 7.5.4 Example 2
     * The following shows a cross-reference section consisting of a single subsection with six entries: four that
     * are in use (objects number 1, 2, 4, and 5) and two that are free (objects number 0 and 3). Object number
     * 3 has been deleted, and the next object created with that object number is given a generation number of 7.
     */
    public function testParseExample2(): void {
        $stream = Stream::fromString(
            <<<EOD
            xref
            0 6
            0000000003 65535 f
            0000000017 00000 n
            0000000081 00000 n
            0000000000 00007 f
            0000000331 00000 n
            0000000409 00000 n
            EOD
        );
        static::assertEquals(
            new CrossReferenceTable(
                new CrossReferenceSubSection(
                    0,
                    6,
                    new CrossReferenceEntryFreeObject(3, 65535),
                    new CrossReferenceEntryInUseObject(17, 0),
                    new CrossReferenceEntryInUseObject(81, 0),
                    new CrossReferenceEntryFreeObject(0, 7),
                    new CrossReferenceEntryInUseObject(331, 0),
                    new CrossReferenceEntryInUseObject(409, 0),
                )
            ),
            CrossReferenceTableParser::parse($stream, 0, $stream->getSizeInBytes()),
        );
    }

    /**
     * 7.5.4 Example 3
     * The following shows a cross-reference section with four subsections, containing a total of five entries. The
     * first subsection contains one entry, for object number 0, which is free. The second subsection contains
     * one entry, for object number 3, which is in use. The third subsection contains two entries, for objects
     * number 23 and 24, both of which are in use. Object number 23 has been reused, as can be seen from the
     * fact that it has a generation number of 2. The fourth subsection contains one entry, for object number 30,
     * which is in use.
     */
    public function testParseExample3(): void {
        $stream = Stream::fromString(
            <<<EOD
            xref
            0 1
            0000000000 65535 f
            3 1
            0000025325 00000 n
            23 2
            0000025518 00002 n
            0000025635 00000 n
            30 1
            0000025777 00000 n
            EOD
        );
        static::assertEquals(
            new CrossReferenceTable(
                (new CrossReferenceSubSection(
                    0,
                    1,
                    new CrossReferenceEntryFreeObject(0, 65535),
                )),
                (new CrossReferenceSubSection(
                    3,
                    1,
                    new CrossReferenceEntryInUseObject(25325, 0),
                )),
                (new CrossReferenceSubSection(
                    23,
                    2,
                    new CrossReferenceEntryInUseObject(25518, 2),
                    new CrossReferenceEntryInUseObject(25635, 0),
                )),
                (new CrossReferenceSubSection(
                    30,
                    1,
                    new CrossReferenceEntryInUseObject(25777, 0),
                )),
            ),
            CrossReferenceTableParser::parse($stream, 0, $stream->getSizeInBytes()),
        );
    }
}
