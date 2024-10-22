<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\ObjectStream\ObjectStreamContent;

use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\FilterNameValue;
use PrinsFrank\PdfParser\Document\Generic\Marker;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

class ObjectStreamContentParser {
    /** @throws ParseFailureException */
    public static function parse(string $content, Dictionary $dictionary): ?string {
        $startStream = strpos($content, Marker::STREAM->value);
        $endStream = strpos($content, Marker::END_STREAM->value);
        if ($startStream === false || $endStream === false) {
            return null;
        }

        $stream = substr($content, $startStream + strlen(Marker::STREAM->value), $endStream - $startStream - strlen(Marker::STREAM->value));
        $streamFilter = $dictionary->getEntryWithKey(DictionaryKey::FILTER)?->value;
        if ($streamFilter instanceof FilterNameValue) {
            $stream = $streamFilter->decode($stream);
        }

        return $stream;
    }
}
