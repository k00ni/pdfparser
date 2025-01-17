<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Text\OperatorString;

use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Document\Object\Decorator\Font;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

enum TextShowingOperator: string {
    case SHOW = 'Tj';
    case MOVE_SHOW = '\'';
    case MOVE_SHOW_SPACING = '"';
    case SHOW_ARRAY = 'TJ';

    public function displayOperands(string $operands, ?Font $font, Document $document): string {
        if ($this === self::SHOW_ARRAY) {
            if (preg_match_all('/\((?<chars>[^)]+)\)(?<offset>-?[0-9]+(.[0-9]+)?)?/', rtrim(ltrim($operands, '['), ']'), $matches, PREG_SET_ORDER) === 0) {
                throw new ParseFailureException('"' . $operands . '"');
            }

            $string = '';
            foreach ($matches as $match) {
                $string .= $match['chars'];

                if ((int) ($match['offset'] ?? 0) < -20) {
                    $string .= ' ';
                }
            }

            return $string;
        }

        $characters = '';
        if (($toUnicodeCMap = $font?->getToUnicodeCMap()) !== null) {
            $characterSets = explode('><', rtrim(ltrim($operands, '<'), '>'));
            foreach ($characterSets as $characterSet) {
                foreach (str_split($characterSet, 2) as $characterGroup) {
                    $characters .= $toUnicodeCMap->toUnicode((int) hexdec($characterGroup)) ?? '';
                }
            }
        }

        return $characters;
    }
}
