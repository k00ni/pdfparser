<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference\Stream;

use PrinsFrank\PdfParser\Document\CrossReference\Source\Section\CrossReferenceSection;
use PrinsFrank\PdfParser\Document\CrossReference\Source\Section\SubSection\CrossReferenceSubSection;
use PrinsFrank\PdfParser\Document\CrossReference\Source\Section\SubSection\Entry\CrossReferenceEntryCompressed;
use PrinsFrank\PdfParser\Document\CrossReference\Source\Section\SubSection\Entry\CrossReferenceEntryFreeObject;
use PrinsFrank\PdfParser\Document\CrossReference\Source\Section\SubSection\Entry\CrossReferenceEntryInUseObject;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParser;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Array\ArrayValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Array\WValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Integer\IntegerValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Generic\Marker;
use PrinsFrank\PdfParser\Document\Object\ObjectStream\ObjectStreamContent\ObjectStreamContentParser;
use PrinsFrank\PdfParser\Exception\MarkerNotFoundException;
use PrinsFrank\PdfParser\Exception\ParseFailureException;
use PrinsFrank\PdfParser\Stream;

class CrossReferenceStreamParser {
    private const HEX_CHARS_IN_BYTE = 2;

    /**
     * @phpstan-assert int<0, max> $startPos
     * @phpstan-assert int<1, max> $nrOfBytes
     */
    public static function parse(Stream $stream, int $startPos, int $nrOfBytes): CrossReferenceSection {
        $dictionary = DictionaryParser::parse($stream, $startPos, $nrOfBytes);
        $dictionaryType = $dictionary->getValueForKey(DictionaryKey::TYPE, TypeNameValue::class);
        if ($dictionaryType !== TypeNameValue::X_REF) {
            throw new ParseFailureException('Expected stream of type xref');
        }

        $wValue = $dictionary->getValueForKey(DictionaryKey::W, WValue::class);
        $startStream = $stream->getStartNextLineAfter(Marker::STREAM, $startPos, $startPos + $nrOfBytes)
            ?? throw new MarkerNotFoundException(Marker::STREAM->value);

        $endStream = $stream->firstPos(Marker::END_STREAM, $startStream, $startPos + $nrOfBytes);
        if ($endStream === null || $endStream > ($startPos + $nrOfBytes)) {
            throw new ParseFailureException(sprintf('Expected end of stream content marked by %s, none found', Marker::END_STREAM->value));
        }

        $entries = [];
        $hexContent = ObjectStreamContentParser::parse($stream, $startStream, $endStream - $startStream - 1, $dictionary);
        foreach (str_split($hexContent, $wValue->getTotalLengthInBytes() * self::HEX_CHARS_IN_BYTE) as $referenceRow) {
            $field1 = CrossReferenceStreamType::tryFrom($typeNr = hexdec(substr($referenceRow, 0, $wValue->lengthRecord1InBytes * self::HEX_CHARS_IN_BYTE)));
            $field2 = hexdec(substr($referenceRow, $wValue->lengthRecord1InBytes * self::HEX_CHARS_IN_BYTE, $wValue->lengthRecord2InBytes * self::HEX_CHARS_IN_BYTE));
            $field3 = hexdec(substr($referenceRow, ($wValue->lengthRecord1InBytes + $wValue->lengthRecord2InBytes) * self::HEX_CHARS_IN_BYTE, $wValue->lengthRecord3InBytes * self::HEX_CHARS_IN_BYTE));

            $entries[] = match ($field1) {
                CrossReferenceStreamType::LINKED_LIST_FREE_OBJECT => new CrossReferenceEntryFreeObject($field2, $field3),
                CrossReferenceStreamType::UNCOMPRESSED_OBJECT => new CrossReferenceEntryInUseObject($field2, $field3),
                CrossReferenceStreamType::COMPRESSED_OBJECT => new CrossReferenceEntryCompressed($field2, $field3),
                null => throw new ParseFailureException(sprintf('Unrecognized CrossReferenceStream type "%s"', $typeNr)),
            };
        }

        /** @var list<int> $startObjNrOfItemsArray where all even items are the start object number and all odd items are the number of objects */
        $startObjNrOfItemsArray = $dictionary->getValueForKey(DictionaryKey::INDEX, ArrayValue::class)?->value
            ?? [0, $dictionary->getValueForKey(DictionaryKey::SIZE, IntegerValue::class)->value];

        $crossReferenceSubSections = [];
        foreach (array_chunk($startObjNrOfItemsArray, 2) as $startNrNrOfObjects) {
            $crossReferenceSubSections[] = new CrossReferenceSubSection($startNrNrOfObjects[0], $startNrNrOfObjects[1], ... array_slice($entries, 0, $startNrNrOfObjects[1]));
            $entries = array_slice($entries, $startNrNrOfObjects[1]);
        }

        return new CrossReferenceSection($dictionary, ... $crossReferenceSubSections);
    }
}
