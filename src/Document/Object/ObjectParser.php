<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParser;
use PrinsFrank\PdfParser\Document\Errors\ErrorCollection;
use PrinsFrank\PdfParser\Document\Object\ObjectStream\ObjectStream;

class ObjectParser {
    public static function parse(?string $decodedStream, ErrorCollection $errorCollection): ObjectItemCollection {
        $objectItemCollection = new ObjectItemCollection();
        if ($decodedStream === null) {
            return $objectItemCollection;
        }

        $firstEOL = strcspn($decodedStream, "\r\n");
        $objectIndicesLine = substr($decodedStream, 0, $firstEOL);
        $items = explode(' ', $objectIndicesLine);
        $objectLocations = [];
        foreach ($items as $key => $value) {
            if ($key % 2 === 1) {
                $objectLocations[$items[$key - 1]] = (int) $value;
            }
        }

        $objectStreamContent = substr($decodedStream, $firstEOL);
        $objectLocationIndices = array_values($objectLocations);
        sort($objectLocationIndices);
        foreach ($objectLocationIndices as $index => $objectOffset) {
            if (array_key_exists($index + 1, $objectLocationIndices)) {
                $objectContent = substr($objectStreamContent, $objectOffset, ($objectLocationIndices[$index + 1] ?? 0) - $objectOffset);
            } else {
                $objectContent = substr($objectStreamContent, $objectOffset);
            }
            $objectId = array_search($objectOffset, $objectLocations, true);
            $objectItemCollection->addObjectItem(
                new ObjectItem(
                    (int) $objectId,
                    $objectContent,
                    DictionaryParser::parse($objectContent, $errorCollection)
                )
            );
        }

        return $objectItemCollection;
    }
}
