<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Text\OperatorString;

use PrinsFrank\PdfParser\Exception\InvalidArgumentException;

enum TextStateOperator: string {
    case CHAR_SIZE = 'Tc';
    case WORD_SPACE = 'Tw';
    case SCALE = 'Tz';
    case LEADING = 'TL';
    case FONT_SIZE = 'Tf';
    case RENDER = 'Tr';
    case RISE = 'Ts';

    public function getFontNumber(string $operand): ?int {
        if ($this !== self::FONT_SIZE) {
            throw new InvalidArgumentException('Can only retrieve font for Tf operator');
        }

        if (!str_starts_with($operand, '/F')) {
            return null;
        }

        if (preg_match('/^\/F(?<fontNumber>[0-9]+)\h+[0-9]+(\.[0-9]+)?$/', $operand, $matches) !== 1) {
            throw new InvalidArgumentException(sprintf('Invalid font operand "%s"', substr($operand, 0, 200)));
        }

        return (int) $matches['fontNumber'];
    }
}
