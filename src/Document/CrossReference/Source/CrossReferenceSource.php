<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference\Source;

use PrinsFrank\PdfParser\Document\CrossReference\Source\Section\CrossReferenceSection;
use PrinsFrank\PdfParser\Document\CrossReference\Source\Section\SubSection\Entry\CrossReferenceEntryCompressed;
use PrinsFrank\PdfParser\Document\CrossReference\Source\Section\SubSection\Entry\CrossReferenceEntryInUseObject;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\DictionaryValueType;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Reference\ReferenceValue;

/** Can be both from a crossReferenceTable or a crossReferenceStream */
class CrossReferenceSource {
    /** @var list<CrossReferenceSection> Where the first is the newest incremental update and the last one is the oldest */
    private readonly array $crossReferenceSections;

    /** @no-named-arguments */
    public function __construct(
        CrossReferenceSection... $crossReferenceSections,
    ) {
        $this->crossReferenceSections = $crossReferenceSections;
    }

    public function getCrossReferenceEntry(int $objNumber): CrossReferenceEntryInUseObject|CrossReferenceEntryCompressed|null {
        foreach ($this->crossReferenceSections as $crossReferenceSection) {
            $crossReferenceEntry = $crossReferenceSection->getCrossReferenceEntry($objNumber);
            if ($crossReferenceEntry !== null) {
                return $crossReferenceEntry;
            }
        }

        return null;
    }

    public function getReferenceForKey(DictionaryKey $dictionaryKey): ?ReferenceValue {
        foreach ($this->crossReferenceSections as $crossReferenceSection) {
            $referenceForKey = $crossReferenceSection->dictionary->getValueForKey($dictionaryKey, ReferenceValue::class);
            if ($referenceForKey !== null) {
                return $referenceForKey;
            }
        }

        return null;
    }
}
