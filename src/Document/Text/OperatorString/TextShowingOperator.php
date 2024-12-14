<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Text\OperatorString;

use PrinsFrank\PdfParser\Document\Object\Decorator\Font;
use PrinsFrank\PdfParser\Exception\RuntimeException;

enum TextShowingOperator: string {
    case SHOW = 'Tj';
    case MOVE_SHOW = '\'';
    case MOVE_SHOW_SPACING = '"';
    case SHOW_ARRAY = 'TJ';

    public function displayOperands(string $operands, ?Font $font): string {
        return match ($this) {
            self::SHOW => rtrim(ltrim($operands, '('), ')'),
            self::MOVE_SHOW => throw new \Exception('To be implemented'),
            self::MOVE_SHOW_SPACING => throw new \Exception('To be implemented'),
            self::SHOW_ARRAY => preg_replace('/\(([^)]+)\)(-?[0-9]+(.[0-9]+)?)?/', '$1', rtrim(ltrim($operands, '['), ']'))
                ?? throw new RuntimeException(),
        };
    }
}
