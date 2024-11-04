<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\ObjectStream\ObjectStreamContent;

use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\FilterNameValue;
use PrinsFrank\PdfParser\Exception\ParseFailureException;
use PrinsFrank\PdfParser\Stream;

class ObjectStreamContentParser {
    /** @throws ParseFailureException */
    public static function parse(Stream $stream, int $startPos, int $nrOfBytes, Dictionary $dictionary): ?string {
        $streamContent = $stream->read($startPos, $nrOfBytes);
        if (($streamFilter = $dictionary->getEntryWithKey(DictionaryKey::FILTER)?->value) instanceof FilterNameValue) {
            $streamContent = $streamFilter->decode($streamContent, $dictionary->getEntryWithKey(DictionaryKey::DECODE_PARAMS)?->value);
        }

        return $streamContent;
    }
}
