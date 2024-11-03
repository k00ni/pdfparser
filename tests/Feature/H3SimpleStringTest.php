<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Tests\Feature;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use PrinsFrank\PdfParser\Document\CrossReference\Source\CrossReferenceSource;
use PrinsFrank\PdfParser\Document\CrossReference\Source\SubSection\CrossReferenceSubSection;
use PrinsFrank\PdfParser\Document\CrossReference\Source\SubSection\Entry\CrossReferenceEntryFreeObject;
use PrinsFrank\PdfParser\Document\CrossReference\Source\SubSection\Entry\CrossReferenceEntryInUseObject;
use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryEntry\DictionaryEntry;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Array\ArrayValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Integer\IntegerValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\SubtypeNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Rectangle\Rectangle;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Reference\ReferenceValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Reference\ReferenceValueArray;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\TextString\TextStringValue;
use PrinsFrank\PdfParser\Document\Object\ObjectItem;
use PrinsFrank\PdfParser\Document\Trailer\Trailer;
use PrinsFrank\PdfParser\Document\Version\Version;
use PrinsFrank\PdfParser\Exception\InvalidArgumentException;
use PrinsFrank\PdfParser\Exception\PdfParserException;
use PrinsFrank\PdfParser\PdfParser;
use PrinsFrank\PdfParser\Stream;

#[CoversNothing]
class H3SimpleStringTest extends TestCase {
    /**
     * @throws InvalidArgumentException
     * @throws PdfParserException
     */
    public function testStructure(): void {
        $document = (new PdfParser())
            ->parse(Stream::openFile(__DIR__ . '/samples/h3-simple-string.pdf'));

        static::assertSame(Version::V1_4, $document->version);
        static::assertEquals(
            new Trailer(
                764,
                750,
                555,
                716,
                new Dictionary(
                    new DictionaryEntry(DictionaryKey::SIZE, new IntegerValue(8)),
                    new DictionaryEntry(DictionaryKey::ROOT, new ReferenceValue(1, 0)),
                ),
            ),
            $document->trailer
        );
        static::assertEquals(
            new CrossReferenceSource(
                null,
                new CrossReferenceSubSection(
                    0,
                    8,
                    new CrossReferenceEntryFreeObject(0, 65535),
                    new CrossReferenceEntryInUseObject(9, 0),
                    new CrossReferenceEntryInUseObject(74, 0),
                    new CrossReferenceEntryInUseObject(120, 0),
                    new CrossReferenceEntryInUseObject(179, 0),
                    new CrossReferenceEntryInUseObject(322, 0),
                    new CrossReferenceEntryInUseObject(417, 0),
                    new CrossReferenceEntryInUseObject(447, 0),
                )
            ),
            $document->crossReferenceSource,
        );
        $obj1 = $document->getObject(1);
        static::assertEquals(
            new ObjectItem(
                1,
                0,
                9,
                74,
            ),
            $obj1,
        );
        static::assertEquals(
            new Dictionary(
                new DictionaryEntry(DictionaryKey::TYPE, TypeNameValue::CATALOG),
                new DictionaryEntry(DictionaryKey::OUTLINES, new ReferenceValue(2, 0)),
                new DictionaryEntry(DictionaryKey::PAGES, new ReferenceValue(3, 0)),
            ),
            $obj1->getDictionary($document->stream)
        );
        $obj2 = $document->getObject(2);
        static::assertEquals(
            new ObjectItem(
                2,
                0,
                74,
                120,
            ),
            $obj2,
        );
        static::assertEquals(
            new Dictionary(
                new DictionaryEntry(DictionaryKey::TYPE, TypeNameValue::OUTLINES),
                new DictionaryEntry(DictionaryKey::COUNT, new IntegerValue(0)),
            ),
            $obj2->getDictionary($document->stream),
        );
        $obj3 = $document->getObject(3);
        static::assertEquals(
            new ObjectItem(
                3,
                0,
                120,
                179,
            ),
            $obj3,
        );
        static::assertEquals(
            new Dictionary(
                new DictionaryEntry(DictionaryKey::TYPE, TypeNameValue::PAGES),
                new DictionaryEntry(DictionaryKey::KIDS, new ReferenceValueArray(new ReferenceValue(4, 0))),
                new DictionaryEntry(DictionaryKey::COUNT, new IntegerValue(1)),
            ),
            $obj3->getDictionary($document->stream),
        );
        $obj4 = $document->getObject(4);
        static::assertEquals(
            new ObjectItem(
                4,
                0,
                179,
                322,
            ),
            $obj4,
        );
        static::assertEquals(
            new Dictionary(
                new DictionaryEntry(DictionaryKey::TYPE, TypeNameValue::PAGE),
                new DictionaryEntry(DictionaryKey::PARENT, new ReferenceValue(3, 0)),
                new DictionaryEntry(DictionaryKey::MEDIABOX, new Rectangle(0.0, 0.0, 0.0, 612.0)),
                new DictionaryEntry(DictionaryKey::CONTENTS, new ReferenceValueArray(new ReferenceValue(5, 0))),
                new DictionaryEntry(DictionaryKey::RESOURCES, new ArrayValue(
                    [
                        new DictionaryEntry(DictionaryKey::PROCSET, new ReferenceValue(6, 0)),
                        new DictionaryEntry(DictionaryKey::FONT, new ArrayValue([new DictionaryEntry(DictionaryKey::F, new ReferenceValue(7, 0))])),
                    ]
                )),
            ),
            $obj4->getDictionary($document->stream),
        );
        $obj5 = $document->getObject(5);
        static::assertEquals(
            new ObjectItem(
                5,
                0,
                322,
                417,
            ),
            $obj5,
        );
        static::assertEquals(
            new Dictionary(
                new DictionaryEntry(DictionaryKey::LENGTH, new IntegerValue(73)),
            ),
            $obj5->getDictionary($document->stream),
        );
        $obj6 = $document->getObject(6);
        static::assertEquals(
            new ObjectItem(
                6,
                0,
                417,
                447,
            ),
            $obj6,
        );
        static::assertNull($obj6->getDictionary($document->stream));
        $obj7 = $document->getObject(7);
        static::assertEquals(
            new ObjectItem(
                7,
                0,
                447,
                716,
            ),
            $obj7,
        );
        static::assertEquals(
            new Dictionary(
                new DictionaryEntry(DictionaryKey::TYPE, TypeNameValue::FONT),
                new DictionaryEntry(DictionaryKey::SUBTYPE, SubtypeNameValue::TYPE_1),
                new DictionaryEntry(DictionaryKey::NAME, new TextStringValue('/F1')),
                new DictionaryEntry(DictionaryKey::BASE_FONT, new TextStringValue('/Helvetica')),
                new DictionaryEntry(DictionaryKey::ENCODING, new TextStringValue('/MacRomanEncoding')),
            ),
            $obj7->getDictionary($document->stream),
        );
    }
}
