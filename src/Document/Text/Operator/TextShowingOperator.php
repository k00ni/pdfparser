<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Text\Operator;

enum TextShowingOperator: string
{
    case SHOW = 'Tj';
    case MOVE_SHOW = '\'';
    case MOVE_SHOW_SPACING = '"';
    case SHOW_ARRAY = 'TJ';
}
