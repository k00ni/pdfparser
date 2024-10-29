<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable\CrossReferenceTable;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParser;
use PrinsFrank\PdfParser\Document\Errors\ErrorCollection;
use PrinsFrank\PdfParser\Document\Generic\Character\DelimiterCharacter;
use PrinsFrank\PdfParser\Document\Generic\Character\WhitespaceCharacter;
use PrinsFrank\PdfParser\Document\Generic\Marker;
use PrinsFrank\PdfParser\Document\Trailer\Trailer;
use PrinsFrank\PdfParser\Exception\ParseFailureException;
use PrinsFrank\PdfParser\Stream;

class ObjectItemParser {
    public static function parseObject(int $objectNumber, CrossReferenceTable $crossReferenceSource, Stream $stream, Trailer $trailer, ErrorCollection $errorCollection): ?ObjectItem {
        $crossReferenceEntry = $crossReferenceSource->getCrossReferenceEntry($objectNumber);
        if ($crossReferenceEntry === null) {
            return null;
        }

        $nextByteOffset = $crossReferenceSource->getNextByteOffset($crossReferenceEntry->byteOffsetInDecodedStream) ?? $trailer->startTrailerMarkerPos;
        $firstLine = $stream->read($crossReferenceEntry->byteOffsetInDecodedStream, ($endOfHeaderLine = $stream->getEndOfCurrentLine($crossReferenceEntry->byteOffsetInDecodedStream, $nextByteOffset)) - $crossReferenceEntry->byteOffsetInDecodedStream);
        $objHeaderParts = explode(WhitespaceCharacter::SPACE->value, $firstLine);
        if (count($objHeaderParts) !== 3 || (int) $objHeaderParts[0] !== $objectNumber || (int) $objHeaderParts[1] !== $crossReferenceEntry->generationNumber || $objHeaderParts[2] !== Marker::OBJ->value) {
            throw new ParseFailureException(sprintf('Expected %d %d %s on first line, got "%s"', $objectNumber, $crossReferenceEntry->generationNumber, Marker::OBJ->value, $firstLine));
        }

        $startDictionaryPos = $stream->strpos(DelimiterCharacter::LESS_THAN_SIGN->value . DelimiterCharacter::LESS_THAN_SIGN->value, $endOfHeaderLine, $nextByteOffset);
        if ($startDictionaryPos !== null) {
            $endDictionaryPos = $stream->strrpos(DelimiterCharacter::GREATER_THAN_SIGN->value . DelimiterCharacter::GREATER_THAN_SIGN->value, $stream->getSizeInBytes() - $nextByteOffset);
            if ($endDictionaryPos === null || $endDictionaryPos < $startDictionaryPos) {
                throw new ParseFailureException(sprintf('Couldn\'t find the end of the dictionary in "%s"', $stream->read($startDictionaryPos, $stream->getSizeInBytes() - $nextByteOffset)));
            }

            $dictionary = DictionaryParser::parse($stream, $startDictionaryPos, $endDictionaryPos - $startDictionaryPos + strlen(DelimiterCharacter::GREATER_THAN_SIGN->value . DelimiterCharacter::GREATER_THAN_SIGN->value), $errorCollection);
        } else {
            $dictionary = null;
        }

        return new ObjectItem(
            $objectNumber,
            $crossReferenceEntry->generationNumber,
            $dictionary
        );
    }
}
