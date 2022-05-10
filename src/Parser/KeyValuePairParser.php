<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Parser;

use PrinsFrank\PdfParser\Enum\DictionaryKey;

class KeyValuePairParser
{
    public static function parse(string $content): array
    {
        $keyValuePairs = [];
        $buffer = $key = '';
        $depth = 0;
        $inKey = false;
        foreach (str_split($content) as $char) {
            if ($char === '/') {
                if ($inKey === true && trim($key) !== '') {
                    $keyValuePairs[$key] = true;
                } else if (trim($key) !== '') {
                    $keyValuePairs[$key] = trim($buffer);
                }

                $buffer = '';
                $inKey = true;
                $key = '';
            } else if ($inKey === true && ctype_alpha($char) === false) {
                $buffer = $char;
                $inKey = false;
            } else if ($inKey === true) {
                $key .= $char;
            } else {
                $buffer .= $char;
            }

            if (str_ends_with($buffer, '<<')) {
                $depth++;
                $buffer = '';

                continue;
            }

            if (str_ends_with($buffer, '>>')) {
                $depth--;
                $keyValuePairs[$key] = trim($buffer);
                $buffer = '';

                continue;
            }

            echo '(' . $char . ', ' . $depth . ', Inkey: ' . ($inKey === true ? 'true' : 'false' ) .'), ' . $buffer . ', Key buffer: "' .  $key . '"' . PHP_EOL;
        }

        return $keyValuePairs;
    }
}
