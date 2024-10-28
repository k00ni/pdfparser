<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Tests\Feature;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable\CrossReferenceSubSection;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable\CrossReferenceTable;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable\Entry\CrossReferenceEntryFreeObject;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable\Entry\CrossReferenceEntryInUseObject;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable\ObjectInUseOrFreeCharacter;
use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryEntry\DictionaryEntry;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Integer\IntegerValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Reference\ReferenceValue;
use PrinsFrank\PdfParser\Document\Trailer\Trailer;
use PrinsFrank\PdfParser\Document\Version\Version;
use PrinsFrank\PdfParser\Exception\InvalidArgumentException;
use PrinsFrank\PdfParser\Exception\PdfParserException;
use PrinsFrank\PdfParser\PdfParser;
use PrinsFrank\PdfParser\Stream;

#[CoversNothing]
class H2MinimalTest extends TestCase {
    /**
     * @throws InvalidArgumentException
     * @throws PdfParserException
     */
    public function testStructure(): void {
        $document = (new PdfParser())
            ->parse(Stream::openFile(__DIR__ . '/samples/h2-minimal.pdf'));

        static::assertSame(Version::V1_4, $document->version);
        static::assertEquals(
            new Trailer(
                598,
                584,
                408,
                550,
                new Dictionary(
                    new DictionaryEntry(DictionaryKey::SIZE, new IntegerValue(7)),
                    new DictionaryEntry(DictionaryKey::ROOT, new ReferenceValue(1, 0)),
                ),
            ),
            $document->trailer
        );
        static::assertEquals(
            new CrossReferenceTable(
                new CrossReferenceSubSection(
                    0,
                    7,
                    new CrossReferenceEntryFreeObject(0, 65535),
                    new CrossReferenceEntryInUseObject(9, 0),
                    new CrossReferenceEntryInUseObject(74, 0),
                    new CrossReferenceEntryInUseObject(120, 0),
                    new CrossReferenceEntryInUseObject(179, 0),
                    new CrossReferenceEntryInUseObject(300, 0),
                    new CrossReferenceEntryInUseObject(384, 0),
                )
            ),
            $document->crossReferenceSource,
        );
    }
}