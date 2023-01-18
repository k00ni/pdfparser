<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Text\Operator;

enum TextStateOperator: string
{
    case CHAR_SIZE = 'Tc';
    case WORD_SPACE = 'Tw';
    case SCALE = 'Tz';
    case LEADING = 'TL';
    case FONT_SIZE = 'Tf';
    case RENDER = 'Tr';
    case RISE = 'Ts';
}
