<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference\Source;

use PrinsFrank\PdfParser\Document\CrossReference\Source\Section\CrossReferenceSection;
use PrinsFrank\PdfParser\Document\CrossReference\Source\Section\SubSection\Entry\CrossReferenceEntryCompressed;
use PrinsFrank\PdfParser\Document\CrossReference\Source\Section\SubSection\Entry\CrossReferenceEntryInUseObject;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Reference\ReferenceValue;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

/** Can be both from a crossReferenceTable or a crossReferenceStream */
class CrossReferenceSource {
    /** @var list<CrossReferenceSection> Where the first is the newest incremental update and the last one is the oldest */
    private readonly array $crossReferenceSections;

    public function __construct(
        CrossReferenceSection... $crossReferenceSections,
    ) {
        $this->crossReferenceSections = $crossReferenceSections;
    }

    public function getCrossReferenceEntry(int $objNumber): CrossReferenceEntryInUseObject|CrossReferenceEntryCompressed|null {
        foreach ($this->crossReferenceSections as $crossReferenceSection) {
            if (($crossReferenceEntry = $crossReferenceSection->getCrossReferenceEntry($objNumber)) !== null) {
                return $crossReferenceEntry;
            }
        }

        return null;
    }

    public function getNextByteOffset(int $currentByteOffset): ?int {
        $byteOffsets = [];
        foreach ($this->crossReferenceSections as $crossReferenceSection) {
            $byteOffsets = [... $byteOffsets, ...$crossReferenceSection->getByteOffsets()];
        }

        sort($byteOffsets);
        foreach ($byteOffsets as $byteOffset) {
            if ($byteOffset > $currentByteOffset) {
                return $byteOffset;
            }
        }

        return null;
    }

    public function getRoot(): ReferenceValue {
        foreach ($this->crossReferenceSections as $crossReferenceSection) {
            if (($rootReference = $crossReferenceSection->dictionary->getEntryWithKey(DictionaryKey::ROOT)->value) instanceof ReferenceValue) {
                return $rootReference;
            }
        }

        throw new ParseFailureException('Unable to locate root in any cross reference section');
    }
}
