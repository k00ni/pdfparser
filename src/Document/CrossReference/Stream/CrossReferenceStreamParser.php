<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference\Stream;

use PrinsFrank\PdfParser\Document\CrossReference\Source\CrossReferenceSource;
use PrinsFrank\PdfParser\Document\CrossReference\Source\Section\CrossReferenceSection;
use PrinsFrank\PdfParser\Document\CrossReference\Source\Section\SubSection\CrossReferenceSubSection;
use PrinsFrank\PdfParser\Document\CrossReference\Source\Section\SubSection\Entry\CrossReferenceEntryCompressed;
use PrinsFrank\PdfParser\Document\CrossReference\Source\Section\SubSection\Entry\CrossReferenceEntryFreeObject;
use PrinsFrank\PdfParser\Document\CrossReference\Source\Section\SubSection\Entry\CrossReferenceEntryInUseObject;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParser;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Array\WValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Generic\Marker;
use PrinsFrank\PdfParser\Document\Object\ObjectStream\ObjectStreamContent\ObjectStreamContentParser;
use PrinsFrank\PdfParser\Exception\ParseFailureException;
use PrinsFrank\PdfParser\Stream;

class CrossReferenceStreamParser {
    private const HEX_CHARS_IN_BYTE = 2;

    /**
     * @param positive-int $startPos
     * @param positive-int $nrOfBytes
     * @throws ParseFailureException
     */
    public static function parse(Stream $stream, int $startPos, int $nrOfBytes): CrossReferenceSection {
        $dictionary = DictionaryParser::parse($stream, $startPos, $nrOfBytes);
        $dictionaryType = $dictionary->getEntryWithKey(DictionaryKey::TYPE)?->value;
        if ($dictionaryType !== TypeNameValue::X_REF) {
            throw new ParseFailureException('Expected stream of type xref, got "' . ($dictionaryType?->name ?? 'null') . '" Dictionary: ' . json_encode($dictionary));
        }

        $wValue = $dictionary->getEntryWithKey(DictionaryKey::W)?->value;
        if ($wValue instanceof WValue === false) {
            throw new ParseFailureException('Missing W value, can\'t decode xref stream.');
        }

        $startStream = $stream->strpos(Marker::STREAM->value, $startPos, $startPos + $nrOfBytes);
        if ($startStream === null || $startStream > ($startPos + $nrOfBytes)) {
            throw new ParseFailureException(sprintf('Expected stream content marked by %s, none found', Marker::STREAM->value));
        }

        $endStream = $stream->strpos(Marker::END_STREAM->value, $startStream, $startPos + $nrOfBytes);
        if ($endStream === null || $endStream > ($startPos + $nrOfBytes)) {
            throw new ParseFailureException(sprintf('Expected end of stream content marked by %s, none found', Marker::END_STREAM->value));
        }

        $startStreamContent = $stream->getStartOfNextLine($startStream, $endStream)
            ?? throw new ParseFailureException('Unable to find start of stream content');

        $entries = [];
        $hexContent = ObjectStreamContentParser::parse($stream, $startStreamContent, $endStream - $startStreamContent - 1, $dictionary);
        foreach (str_split($hexContent, $wValue->getTotalLengthInBytes() * self::HEX_CHARS_IN_BYTE) as $referenceRow) {
            $field1 = CrossReferenceStreamType::tryFrom($typeNr = hexdec(substr($referenceRow, 0, $wValue->getLengthRecord1InBytes() * self::HEX_CHARS_IN_BYTE)));
            $field2 = hexdec(substr($referenceRow, $wValue->getLengthRecord1InBytes() * self::HEX_CHARS_IN_BYTE, $wValue->getLengthRecord2InBytes() * self::HEX_CHARS_IN_BYTE));
            $field3 = hexdec(substr($referenceRow, ($wValue->getLengthRecord1InBytes() + $wValue->getLengthRecord2InBytes()) * self::HEX_CHARS_IN_BYTE, $wValue->getLengthRecord3InBytes() * self::HEX_CHARS_IN_BYTE));

            $entries[] = match ($field1) {
                CrossReferenceStreamType::LINKED_LIST_FREE_OBJECT => new CrossReferenceEntryFreeObject($field2, $field3),
                CrossReferenceStreamType::UNCOMPRESSED_OBJECT => new CrossReferenceEntryInUseObject($field2, $field3),
                CrossReferenceStreamType::COMPRESSED_OBJECT => new CrossReferenceEntryCompressed($field2, $field3),
                null => throw new ParseFailureException(sprintf('Unrecognized CrossReferenceStream type "%s"', $typeNr)),
            };
        }

        /** @var list<int> $startObjNrOfItemsArray where all even items are the start object number and all odd items are the number of objects */
        $startObjNrOfItemsArray = $dictionary->getEntryWithKey(DictionaryKey::INDEX)->value->value
            ?? [0, $dictionary->getEntryWithKey(DictionaryKey::SIZE)->value->value];

        $crossReferenceSubSections = [];
        foreach (array_chunk($startObjNrOfItemsArray, 2) as $startNrNrOfObjects) {
            $crossReferenceSubSections[] = new CrossReferenceSubSection($startNrNrOfObjects[0], $startNrNrOfObjects[1], ... array_slice($entries, 0, $startNrNrOfObjects[1]));
            $entries = array_slice($entries, $startNrNrOfObjects[1]);
        }

        return new CrossReferenceSection($dictionary, ... $crossReferenceSubSections);
    }
}
