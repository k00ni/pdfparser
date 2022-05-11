<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Parser;

class KeyValuePairParser
{
    private const CONTEXT_ROOT                = 0;
    private const CONTEXT_KEY                 = 1;
    private const CONTEXT_KEY_VALUE_SEPARATOR = 2;
    private const CONTEXT_VALUE               = 3;

    public static function parse(string $content): array
    {
        $keyValuePairs = [];
        $depth = 0;
        $valueBuffer = $keyBuffer = [$depth => ''];
        $context = [$depth =>self::CONTEXT_ROOT];
        $previousChar = null;
        foreach (str_split($content) as $char) {
            $codePointChar = ord($char);
            if ($char === '/') {
                if ($context[$depth] === self::CONTEXT_ROOT) {
                    $context[$depth] = self::CONTEXT_KEY;
                } elseif ($context[$depth] === self::CONTEXT_KEY) {
                    $valueBuffer[$depth] = '';
                    $context[$depth] = self::CONTEXT_VALUE;
                } elseif ($context[$depth] === self::CONTEXT_VALUE) {
                    $keyValuePairs = static::assignNested($keyValuePairs, $depth, $keyBuffer, $valueBuffer);
                    $keyBuffer[$depth] = '';
                    $valueBuffer[$depth] = '';
                    $context[$depth] = self::CONTEXT_KEY;
                }
            } else if ($context[$depth] === self::CONTEXT_KEY && (($codePointChar >= 65 && $codePointChar <= 90) || ($codePointChar >= 97 && $codePointChar <= 122)) === false) {
                $context[$depth] = self::CONTEXT_KEY_VALUE_SEPARATOR;
            } else if ($context[$depth] === self::CONTEXT_KEY_VALUE_SEPARATOR) {
                $context[$depth] = self::CONTEXT_VALUE;
            }

            if ($char === '<' && $previousChar === '<') {
                $depth++;
                $valueBuffer[$depth] = '';
                $keyBuffer[$depth] = '';
                $context[$depth] = self::CONTEXT_ROOT;
            }

            if ($char === '>' && $previousChar === '>') {
                $keyValuePairs = static::assignNested($keyValuePairs, $depth, $keyBuffer, $valueBuffer);
                unset($valueBuffer[$depth], $keyBuffer[$depth], $context[$depth]);
                $depth--;
            }

            switch ($context[$depth]) {
                case self::CONTEXT_KEY:
                    $keyBuffer[$depth] .= $char;
                    break;
                case self::CONTEXT_VALUE:
                case self::CONTEXT_KEY_VALUE_SEPARATOR:
                    $valueBuffer[$depth] .= $char;
                    break;
            }

            $previousChar = $char;
        }

        if (trim($valueBuffer[$depth]) !== '' && trim($keyBuffer[$depth]) !== '') {
            $keyValuePairs = static::assignNested($keyValuePairs, $depth, $keyBuffer, $valueBuffer);
        }

        return $keyValuePairs;
    }

    private static function assignNested(mixed $keyValuePairs, int $depth, array $keyBuffer, array $valueBuffer)
    {
        $currentKey = trim($keyBuffer[$depth]);
        $value = trim($valueBuffer[$depth], " \t\n\r\0\x0B<>");
        if ($value === '' || $currentKey === '') {
            return $keyValuePairs;
        }

        if ($depth === 1) {
            $keyValuePairs[$currentKey] = $value;

            return $keyValuePairs;
        }

        $pointer = &$keyValuePairs;
        $pointerDepth = 1;
        while ($pointerDepth < $depth) {
            $pointer = &$keyValuePairs[trim($keyBuffer[$pointerDepth])];
            $pointerDepth++;
        }

        $pointer[$currentKey] = $value;
        return $keyValuePairs;
    }
}
