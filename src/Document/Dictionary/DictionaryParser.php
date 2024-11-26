<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary;

use PrinsFrank\PdfParser\Document\Generic\Parsing\RollingCharBuffer;
use PrinsFrank\PdfParser\Exception\RuntimeException;
use PrinsFrank\PdfParser\Stream;

class DictionaryParser {
    public static function parse(Stream $stream, int $startPos, int $nrOfBytes): Dictionary {
        $rollingCharBuffer = new RollingCharBuffer(3);
        $dictionaryArray = [];
        $nestingLocation = [];
        $inKey = $inBetweenKeyValue = $inValue = false;
        $valueBuffer = $keyBuffer = '';
        foreach ($stream->chars($startPos, $nrOfBytes) as $char) {
            $rollingCharBuffer->next($char);
            if (self::incrementsNesting($rollingCharBuffer) && $keyBuffer !== '') {
                $nestingLocation[] = trim($keyBuffer);
                $inKey = $inValue = false;
                $keyBuffer = $valueBuffer = '';
            } elseif (self::decrementNesting($rollingCharBuffer)) {
                array_pop($nestingLocation);
            } else if (!$inValue && !$inKey && !$inBetweenKeyValue && self::entersKey($rollingCharBuffer)) {
                $inKey = true;
            } elseif ($inKey && self::startsValue($rollingCharBuffer)) {
                $inKey = false;
                $inValue = true;
            } elseif ($inKey && self::exitsKey($rollingCharBuffer)) {
                $inKey = false;
                $inBetweenKeyValue = true;
            } elseif ($inBetweenKeyValue) {
                $inBetweenKeyValue = false;
                $inValue = true;
            } elseif ($inValue && self::exitsValue($rollingCharBuffer)) {
                $pointer = &$dictionaryArray;
                foreach ($nestingLocation as $nestingLocationItem) {
                    $pointer = &$pointer[$nestingLocationItem];
                }
                $pointer[$keyBuffer] = trim($valueBuffer);
                $keyBuffer = $valueBuffer = '';
                $inValue = false;
                if (self::entersKey($rollingCharBuffer)) {
                    $inKey = true;
                }
            }

            match ([$inKey, $inValue]) {
                [false, false] => null,
                [false, true] => $valueBuffer .= $char,
                [true, false] => $keyBuffer .= $char,
                [true, true] => throw new RuntimeException('Can\'t be both in a key and a value'),
            };

        }

        return DictionaryFactory::fromArray($dictionaryArray);
    }

    private static function incrementsNesting(RollingCharBuffer $rollingCharBuffer): bool {
        return $rollingCharBuffer->seenString('<<')
            && !$rollingCharBuffer->seenString('\<<');
    }

    private static function decrementNesting(RollingCharBuffer $rollingCharBuffer): bool {
        return $rollingCharBuffer->seenString('>>')
            && !$rollingCharBuffer->seenString('\>>');
    }

    private static function entersKey(RollingCharBuffer $rollingCharBuffer): bool {
        return $rollingCharBuffer->getCurrentCharacter() === '/';
    }

    private static function exitsKey(RollingCharBuffer $rollingCharBuffer): bool {
        return $rollingCharBuffer->getCurrentCharacter() === ' ';
    }

    private static function startsValue(RollingCharBuffer $rollingCharBuffer): bool {
        return in_array($rollingCharBuffer->getCurrentCharacter(), ['(', '[', '/', '<'], true);
    }

    private static function exitsValue(RollingCharBuffer $rollingCharBuffer): bool {
        return in_array($rollingCharBuffer->getCurrentCharacter(), ["\r", "\n"], true);
    }
}
