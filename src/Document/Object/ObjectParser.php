<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParser;
use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Document\Object\ObjectStream\ObjectStream;

class ObjectParser
{
    /** @return ObjectItem[] */
    public static function parse(Document $document, ObjectStream $objectStream): array
    {
        $numberOfItemsEntry = $objectStream->dictionary->getEntryWithKey(DictionaryKey::N);
        if ($numberOfItemsEntry === null || $objectStream->decodedStream === null) {
            return []; // No objects in this object stream
        }
        $firstEOL = strcspn($objectStream->decodedStream, "\r\n");
        $objectIndicesLine = substr($objectStream->decodedStream, 0, $firstEOL);
        $items = explode(' ', $objectIndicesLine);
        $objectLocations = [];
        foreach ($items as $key => $value) {
            if ($key % 2 === 1) {
                $objectLocations[$items[$key - 1]] = (int) $value;
            }
        }

        $objectStreamContent = substr($objectStream->decodedStream, $firstEOL);
        $objectLocationIndices = array_values($objectLocations);
        sort($objectLocationIndices);
        $objectItems = [];
        foreach ($objectLocationIndices as $index => $objectOffset) {
            if (array_key_exists($index + 1, $objectLocationIndices)) {
                $objectContent = substr($objectStreamContent, $objectOffset, ($objectLocationIndices[$index + 1] ?? 0) - $objectOffset);
            } else {
                $objectContent = substr($objectStreamContent, $objectOffset);
            }
            $objectId = array_search($objectOffset, $objectLocations, true);
            $objectItems[] = new ObjectItem(
                (int) $objectId,
                $objectContent,
                DictionaryParser::parse($document, $objectContent)
            );
        }

        return $objectItems;
    }
}
