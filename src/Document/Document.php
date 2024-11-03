<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document;

use PrinsFrank\PdfParser\Document\CrossReference\Source\CrossReferenceSource;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceStream\CrossReferenceStream;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Reference\ReferenceValue;
use PrinsFrank\PdfParser\Document\Object\ObjectItem;
use PrinsFrank\PdfParser\Document\Object\ObjectItemParser;
use PrinsFrank\PdfParser\Document\Trailer\Trailer;
use PrinsFrank\PdfParser\Document\Version\Version;
use PrinsFrank\PdfParser\Stream;

final class Document {
    public function __construct(
        public readonly Stream               $stream,
        public readonly Version              $version,
        public readonly CrossReferenceSource $crossReferenceSource,
        public readonly Trailer              $trailer,
    ) {
    }

    public function getCatalog(): ?ObjectItem {
        /** @var ReferenceValue|null $catalogReference */
        $catalogReference = $this->crossReferenceSource instanceof CrossReferenceStream
            ? $this->crossReferenceSource->dictionary->getEntryWithKey(DictionaryKey::ROOT)->value
            : $this->trailer->dictionary->getEntryWithKey(DictionaryKey::ROOT)->value;
        if ($catalogReference instanceof ReferenceValue === false) {
            return null;
        }

        return $this->getObject($catalogReference->objectNumber);
    }

    public function getObject(int $objectNumber): ?ObjectItem {
        return ObjectItemParser::parseObject(
            $objectNumber,
            $this->crossReferenceSource,
            $this->stream,
            $this->trailer,
        );
    }
}
