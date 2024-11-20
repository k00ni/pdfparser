<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\Item\CompressedObject\CompressedObjectContent;

use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name\FilterNameValue;
use PrinsFrank\PdfParser\Stream;

class CompressedObjectContentParser {
    public static function parse(Stream $stream, int $startPos, int $nrOfBytes, Dictionary $dictionary): string {
        $streamContent = $stream->read($startPos, $nrOfBytes);
        if (($streamFilter = $dictionary->getValueForKey(DictionaryKey::FILTER, FilterNameValue::class)) !== null) {
            $streamContent = $streamFilter->decode($streamContent, $dictionary->getValueForKey(DictionaryKey::DECODE_PARMS, Dictionary::class));
        }

        return $streamContent;
    }
}
