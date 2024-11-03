<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference\Stream;

use PrinsFrank\PdfParser\Document\CrossReference\Source\CrossReferenceSource;
use PrinsFrank\PdfParser\Document\CrossReference\Source\SubSection\CrossReferenceSubSection;
use PrinsFrank\PdfParser\Document\CrossReference\Source\SubSection\Entry\CompressedObjectEntry;
use PrinsFrank\PdfParser\Document\CrossReference\Source\SubSection\Entry\CrossReferenceEntryFreeObject;
use PrinsFrank\PdfParser\Document\CrossReference\Source\SubSection\Entry\CrossReferenceEntryInUseObject;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParser;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Generic\Marker;
use PrinsFrank\PdfParser\Document\Object\ObjectStream\ObjectStreamContent\ObjectStreamContentParser;
use PrinsFrank\PdfParser\Exception\ParseFailureException;
use PrinsFrank\PdfParser\Stream;

class CrossReferenceStreamParser {
    /**
     * @param positive-int $startPos
     * @param positive-int $nrOfBytes
     * @throws ParseFailureException
     */
    public static function parse(Stream $stream, int $startPos, int $nrOfBytes): CrossReferenceSource {
        $dictionary = DictionaryParser::parse($stream, $startPos, $nrOfBytes);
        $dictionaryType = $dictionary->getEntryWithKey(DictionaryKey::TYPE)?->value;
        if ($dictionaryType !== TypeNameValue::X_REF) {
            throw new ParseFailureException('Expected stream of type xref, got "' . ($dictionaryType?->name ?? 'null') . '" Dictionary: ' . json_encode($dictionary));
        }

        $wValue = $dictionary->getEntryWithKey(DictionaryKey::W)?->value?->value;
        if ($wValue === null) {
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

        $byteLengthRecord1 = ((int) ($wValue[0] ?? 0)) * 2;
        $byteLengthRecord2 = ((int) ($wValue[1] ?? 0)) * 2;
        $byteLengthRecord3 = ((int) ($wValue[2] ?? 0)) * 2;
        $entries = [];
        foreach (str_split(bin2hex(ObjectStreamContentParser::parse($stream, $startStream + strlen(Marker::STREAM->value), $endStream - $startStream - strlen(Marker::STREAM->value), $dictionary)), $byteLengthRecord1 + $byteLengthRecord2 + $byteLengthRecord3) as $referenceRow) {
            $field1 = CrossReferenceStreamType::tryFrom($typeNr = hexdec(substr($referenceRow, 0, $byteLengthRecord1)));
            $field2 = hexdec(substr($referenceRow, $byteLengthRecord1, $byteLengthRecord2));
            $field3 = hexdec(substr($referenceRow, $byteLengthRecord2 + $byteLengthRecord1, $byteLengthRecord3));

            $entries[] = match ($field1) {
                CrossReferenceStreamType::LINKED_LIST_FREE_OBJECT => new CrossReferenceEntryFreeObject($field2, $field3),
                CrossReferenceStreamType::UNCOMPRESSED_OBJECT => new CrossReferenceEntryInUseObject($field2, $field3),
                CrossReferenceStreamType::COMPRESSED_OBJECT => new CompressedObjectEntry($field2, $field3),
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

        return new CrossReferenceSource($dictionary, ... $crossReferenceSubSections);
    }
}
