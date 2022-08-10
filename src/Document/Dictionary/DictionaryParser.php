<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary;

use Exception;
use PrinsFrank\PdfParser\Document\Character\DelimiterCharacter;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

class DictionaryParser
{
    private const CONTEXT_ROOT                = 0;
    private const CONTEXT_KEY                 = 1;
    private const CONTEXT_KEY_VALUE_SEPARATOR = 2;
    private const CONTEXT_VALUE               = 3;
    private const CONTEXT_VALUE_EXPLICIT      = 4;

    /**
     * @throws ParseFailureException
     */
    public static function parse(string $content): Dictionary
    {
        $dictionaryArray = [];
        $depth = 0;
        $valueBuffer = $keyBuffer = [$depth => ''];
        $context = [$depth => self::CONTEXT_ROOT];
        $previousChar = null;
        foreach (str_split($content) as $index => $char) {
            if ($depth > 0) {
                $codePointChar = ord($char);
                if ($char === DelimiterCharacter::SOLIDUS->value) {
                    if ($context[$depth] === self::CONTEXT_ROOT) {
                        $context[$depth] = self::CONTEXT_KEY;
                    } elseif ($context[$depth] === self::CONTEXT_KEY) {
                        $valueBuffer[$depth] = '';
                        $context[$depth]     = self::CONTEXT_VALUE;
                    } elseif ($context[$depth] === self::CONTEXT_VALUE) {
                        $dictionaryArray     = static::assignNested($dictionaryArray, $depth, $keyBuffer, $valueBuffer);
                        $keyBuffer[$depth]   = '';
                        $valueBuffer[$depth] = '';
                        $context[$depth]     = self::CONTEXT_KEY;
                    }
                } else if ($context[$depth] === self::CONTEXT_KEY && (($codePointChar >= 65 && $codePointChar <= 90) || ($codePointChar >= 97 && $codePointChar <= 122) || $codePointChar === 46) === false) {
                    $context[$depth] = self::CONTEXT_KEY_VALUE_SEPARATOR;
                } else if (in_array($context[$depth], [self::CONTEXT_VALUE, self::CONTEXT_KEY_VALUE_SEPARATOR]) && $char === DelimiterCharacter::RIGHT_PARENTHESIS->value) {
                    $context[$depth] = self::CONTEXT_VALUE_EXPLICIT;
                } else if ($context[$depth] === self::CONTEXT_KEY_VALUE_SEPARATOR) {
                    $context[$depth] = self::CONTEXT_VALUE;
                } else if ($context[$depth] === self::CONTEXT_VALUE_EXPLICIT && $char === DelimiterCharacter::LEFT_PARENTHESIS->value) {
                    $context[$depth] = self::CONTEXT_VALUE;
                }
            }

            if ($char === DelimiterCharacter::LESS_THAN_SIGN->value && $previousChar === DelimiterCharacter::LESS_THAN_SIGN->value) {
                $depth++;
                $valueBuffer[$depth] = '';
                $keyBuffer[$depth] = '';
                $context[$depth] = self::CONTEXT_ROOT;
            }

            if ($char === DelimiterCharacter::GREATER_THAN_SIGN->value && $previousChar === DelimiterCharacter::GREATER_THAN_SIGN->value) {
                $dictionaryArray = static::assignNested($dictionaryArray, $depth, $keyBuffer, $valueBuffer);
                unset($valueBuffer[$depth], $keyBuffer[$depth], $context[$depth]);
                $depth--;
            }

            if ($depth < 0) {
                throw new ParseFailureException('Traversed out of bounds in content "' . substr($content, $index - 10, 20) . '" at position "' . $index . '" with current char "' . $char . '" and previous char "' . $previousChar . '"');
            }

            switch ($context[$depth]) {
                case self::CONTEXT_KEY:
                    $keyBuffer[$depth] .= $char;
                    break;
                case self::CONTEXT_VALUE:
                case self::CONTEXT_VALUE_EXPLICIT:
                case self::CONTEXT_KEY_VALUE_SEPARATOR:
                    $valueBuffer[$depth] .= $char;
                    break;
            }

            $previousChar = $char;
        }

        if (trim($valueBuffer[$depth]) !== '' && trim($keyBuffer[$depth]) !== '') {
            $dictionaryArray = static::assignNested($dictionaryArray, $depth, $keyBuffer, $valueBuffer);
        }

        try {
            $dictionary = DictionaryFactory::fromArray($dictionaryArray);
        } catch (Exception $e) {
            throw new Exception('Unable to create dictionary for string "' . $content . '": ' . $e->getMessage());
        }

        return $dictionary;
    }

    private static function assignNested(array $dictionaryArray, int $depth, array $keyBuffer, array $valueBuffer): array
    {
        $currentKey = trim($keyBuffer[$depth]);
        $value = trim($valueBuffer[$depth], " \t\n\r\0\x0B<>");
        if ($value === '' || $currentKey === '') {
            return $dictionaryArray;
        }

        $pointer = &$dictionaryArray;
        $pointerDepth = 1;
        while ($pointerDepth < $depth) {
            $pointer = &$dictionaryArray[trim($keyBuffer[$pointerDepth])];
            $pointerDepth++;
        }

        $pointer[$currentKey] = $value;
        return $dictionaryArray;
    }
}
