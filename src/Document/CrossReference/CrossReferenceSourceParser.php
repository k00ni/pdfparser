<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference;

use PrinsFrank\PdfParser\Document\Generic\Character\ObjectInUseOrFreeCharacter;
use PrinsFrank\PdfParser\Document\Generic\Character\WhitespaceCharacter;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceStream\CrossReferenceData;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceStream\CrossReferenceStream;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable\CrossReferenceEntry;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable\CrossReferenceSubSection;
use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceTable\CrossReferenceTable;
use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParser;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Document\Generic\Marker;
use PrinsFrank\PdfParser\Document\Object\ObjectStream\ObjectStreamContent\ObjectStreamContentParser;
use PrinsFrank\PdfParser\Exception\InvalidCrossReferenceLineException;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

class CrossReferenceSourceParser
{
    /**
     * @throws ParseFailureException
     */
    public static function parse(Document $document): CrossReferenceSource
    {
        $content = substr($document->content, $document->trailer->byteOffsetLastCrossReferenceSection, $document->trailer->startTrailerMarkerPos - $document->trailer->byteOffsetLastCrossReferenceSection);
        $dictionary = DictionaryParser::parse($content);
        if ($dictionary->getEntryWithKey(DictionaryKey::TYPE)?->value === TypeNameValue::X_REF) {
            return self::parseStream($dictionary, $content);
        }

        return static::parseTable($content);
    }

    /**
     * @throws InvalidCrossReferenceLineException
     */
    public static function parseTable(string $content): CrossReferenceTable
    {
        $crossReferenceSubSection = null;
        $crossReferenceSubSections = [];
        $content = str_replace([WhitespaceCharacter::CARRIAGE_RETURN->value], WhitespaceCharacter::LINE_FEED->value, $content);
        foreach (explode(WhitespaceCharacter::LINE_FEED->value, $content) as $index => $line) {
            if (($index === 0 && $line === Marker::XREF->value)
                || trim($line) === '') {
                continue;
            }

            $sections = explode(WhitespaceCharacter::SPACE->value, trim($line));
            switch (count($sections)) {
                case 2:
                    $crossReferenceSubSection = new CrossReferenceSubSection((int) $sections[0], (int) $sections[1]);
                    $crossReferenceSubSections[] = $crossReferenceSubSection;
                    break;
                case 3:
                    $crossReferenceSubSection->addCrossReferenceEntry(new CrossReferenceEntry((int) $sections[0], (int) $sections[1], ObjectInUseOrFreeCharacter::from(trim($sections[2]))));
                    break;
                default:
                    throw new InvalidCrossReferenceLineException('Invalid line "' . trim($line) . '", 2 or 3 sections expected, "' . count($sections) . '" found: ' . json_encode($sections, JSON_THROW_ON_ERROR));
            }
        }

        return new CrossReferenceTable($crossReferenceSubSections);
    }

    /**
     * @throws ParseFailureException
     */
    public static function parseStream(Dictionary $dictionary, string $content): CrossReferenceStream
    {
        $dictionaryType = $dictionary->getEntryWithKey(DictionaryKey::TYPE)?->value;
        if ($dictionaryType !== TypeNameValue::X_REF) {
            throw new ParseFailureException('Expected stream of type xref, got "' . ($dictionaryType?->name ?? 'null') . '" Dictionary: ' . json_encode($dictionary));
        }

        $wValue = $dictionary->getEntryWithKey(DictionaryKey::W)?->value?->value;
        if ($wValue === null) {
            throw new ParseFailureException('Missing W value, can\'t decode stream.');
        }

        $byteLengthRecord1 = ($wValue[0] ?? 0) * 2;
        $byteLengthRecord2 = ($wValue[1] ?? 0) * 2;
        $byteLengthRecord3 = ($wValue[2] ?? 0) * 2;
        $crossReferenceStream = new CrossReferenceStream();
        foreach (str_split(bin2hex(ObjectStreamContentParser::parse($content, $dictionary)), $byteLengthRecord1 + $byteLengthRecord2 + $byteLengthRecord3) as $referenceRow) {
            $crossReferenceStream->addData(
                new CrossReferenceData(
                    substr($referenceRow, 0, $byteLengthRecord1),
                    substr($referenceRow, $byteLengthRecord1, $byteLengthRecord2),
                    substr($referenceRow, $byteLengthRecord2 + $byteLengthRecord1, $byteLengthRecord3),
                )
            );
        }

        return $crossReferenceStream;
    }
}
