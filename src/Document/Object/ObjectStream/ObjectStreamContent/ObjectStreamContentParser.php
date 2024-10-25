<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\ObjectStream\ObjectStreamContent;

use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\FilterNameValue;
use PrinsFrank\PdfParser\Document\Generic\Marker;
use PrinsFrank\PdfParser\Exception\ParseFailureException;
use PrinsFrank\PdfParser\Pdf;

class ObjectStreamContentParser {
    /** @throws ParseFailureException */
    public static function parse(Pdf $pdf, int $startPos, int $nrOfBytes, Dictionary $dictionary): ?string {
        $startStream = $pdf->strpos(Marker::STREAM->value, $startPos);
        $endStream = $pdf->strpos(Marker::END_STREAM->value, $startStream);
        if ($startStream === null || $endStream === null) {
            return null;
        }

        $stream = $pdf->read($startStream + strlen(Marker::STREAM->value), $endStream - $startStream - strlen(Marker::STREAM->value));
        $streamFilter = $dictionary->getEntryWithKey(DictionaryKey::FILTER)?->value;
        if ($streamFilter instanceof FilterNameValue) {
            $stream = $streamFilter->decode($stream);
        }

        return $stream;
    }
}
