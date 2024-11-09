<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\ObjectStream;

use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Generic\Character\WhitespaceCharacter;
use PrinsFrank\PdfParser\Document\Generic\Marker;
use PrinsFrank\PdfParser\Document\Generic\Parsing\InfiniteBuffer;
use PrinsFrank\PdfParser\Document\Object\ObjectStream\ObjectStreamContent\ObjectStreamContentParser;
use PrinsFrank\PdfParser\Exception\MarkerNotFoundException;
use PrinsFrank\PdfParser\Exception\ParseFailureException;
use PrinsFrank\PdfParser\Stream;

class ObjectStreamDataParser {
    public static function parse(Stream $stream, int $startOffsetObject, int $endOffsetObject, Dictionary $dictionary) {
        $startStreamPos = $stream->getStartNextLineAfter(Marker::STREAM, $startOffsetObject, $endOffsetObject)
            ?? throw new MarkerNotFoundException(Marker::STREAM->value);

        $endStreamPos = $stream->firstPos(Marker::END_STREAM, $startStreamPos, $endOffsetObject)
            ?? throw new MarkerNotFoundException(Marker::END_STREAM->value);
        $eolPos = $stream->getEndOfCurrentLine($endStreamPos - 1, $endOffsetObject)
            ?? throw new MarkerNotFoundException(WhitespaceCharacter::LINE_FEED->value);

        $content = ObjectStreamContentParser::parse($stream, $startStreamPos, $eolPos - $startStreamPos, $dictionary);
        $buffer = new InfiniteBuffer();
        $previousObjectNumber = null;
        $byteOffsets = [];
        foreach (str_split(substr($content, 0, $dictionary->getValueForKey(DictionaryKey::FIRST)->value - 1), 2) as $char) {
            $decodedChar = chr(hexdec($char));
            if (WhitespaceCharacter::tryFrom($decodedChar) !== null) {
                $numberInBuffer = $buffer->__toString();
                if ($numberInBuffer !== (string)(int) $numberInBuffer) {
                    throw new ParseFailureException(sprintf('Number "%s" in buffer is not a valid number', $numberInBuffer));
                }

                if ($previousObjectNumber !== null) {
                    $byteOffsets[$previousObjectNumber] = $numberInBuffer;
                    $previousObjectNumber = null;
                } else {
                    $previousObjectNumber = $numberInBuffer;
                }

                $buffer->flush();
                continue;
            }

            $buffer->addChar($decodedChar);
        }

        return new ObjectStreamData($byteOffsets);
    }
}