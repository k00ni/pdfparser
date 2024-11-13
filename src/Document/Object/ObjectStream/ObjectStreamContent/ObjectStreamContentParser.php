<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\ObjectStream\ObjectStreamContent;

use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Array\ArrayValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\FilterNameValue;
use PrinsFrank\PdfParser\Stream;

class ObjectStreamContentParser {
    public static function parse(Stream $stream, int $startPos, int $nrOfBytes, Dictionary $dictionary): ?string {
        $streamContent = $stream->read($startPos, $nrOfBytes);
        if (($streamFilter = $dictionary->getValueForKey(DictionaryKey::FILTER, FilterNameValue::class)) !== null) {
            $streamContent = $streamFilter->decode($streamContent, $dictionary->getValueForKey(DictionaryKey::DECODE_PARAMS, ArrayValue::class));
        }

        return $streamContent;
    }
}
