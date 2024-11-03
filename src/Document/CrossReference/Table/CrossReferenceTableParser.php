<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference\Table;

use PrinsFrank\PdfParser\Document\CrossReference\Source\CrossReferenceSource;
use PrinsFrank\PdfParser\Document\CrossReference\Source\SubSection\CrossReferenceSubSection;
use PrinsFrank\PdfParser\Document\CrossReference\Source\SubSection\Entry\CrossReferenceEntryFreeObject;
use PrinsFrank\PdfParser\Document\CrossReference\Source\SubSection\Entry\CrossReferenceEntryInUseObject;
use PrinsFrank\PdfParser\Document\Generic\Character\WhitespaceCharacter;
use PrinsFrank\PdfParser\Document\Generic\Marker;
use PrinsFrank\PdfParser\Exception\InvalidCrossReferenceLineException;
use PrinsFrank\PdfParser\Stream;

class CrossReferenceTableParser {
    /** @throws InvalidCrossReferenceLineException */
    public static function parse(Stream $stream, int $startPos, int $nrOfBytes): CrossReferenceSource {
        $objectNumber = $nrOfEntries = null;
        $crossReferenceSubSections = $crossReferenceEntries = [];
        $content = trim($stream->read($startPos + strlen(Marker::XREF->value . PHP_EOL), $nrOfBytes - strlen(Marker::XREF->value . PHP_EOL)));
        $content = str_replace([WhitespaceCharacter::CARRIAGE_RETURN->value, WhitespaceCharacter::LINE_FEED->value . WhitespaceCharacter::LINE_FEED->value], WhitespaceCharacter::LINE_FEED->value, $content);
        foreach (explode(WhitespaceCharacter::LINE_FEED->value, $content) as $line) {
            $sections = explode(WhitespaceCharacter::SPACE->value, trim($line));
            switch (count($sections)) {
                case 2:
                    if ($objectNumber !== null && $nrOfEntries !== null) {
                        $crossReferenceSubSections[] = new CrossReferenceSubSection($objectNumber, $nrOfEntries, ... $crossReferenceEntries); // Use previous objectNr and nrOfEntries
                    }
                    $crossReferenceEntries = [];
                    $objectNumber = (int) $sections[0];
                    $nrOfEntries = (int) $sections[1];
                    break;
                case 3:
                    $crossReferenceEntries[] = match (CrossReferenceTableInUseOrFree::from(trim($sections[2]))) {
                        CrossReferenceTableInUseOrFree::IN_USE => new CrossReferenceEntryInUseObject((int) $sections[0], (int) $sections[1]),
                        CrossReferenceTableInUseOrFree::FREE => new CrossReferenceEntryFreeObject((int) $sections[0], (int) $sections[1]),
                    };
                    break;
                default:
                    throw new InvalidCrossReferenceLineException(sprintf('Invalid line "%s", 2 or 3 sections expected, %d found', substr(trim($line), 0, 30), count($sections)));
            }
        }

        if ($objectNumber !== null && $nrOfEntries !== null) {
            $crossReferenceSubSections[] = new CrossReferenceSubSection($objectNumber, $nrOfEntries, ... $crossReferenceEntries);
        }

        return new CrossReferenceSource(null, ... $crossReferenceSubSections);
    }
}
