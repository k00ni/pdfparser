<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Generic\Character;

/**
 * @see PDF 32000-1:2008 7.2.2 Table 1
 */
enum WhitespaceCharacter: string
{
    case NULL            = "\000";
    case HORIZONTAL_TAB  = "\t";
    case LINE_FEED       = "\n";
    case FORM_FEED       = "\f";
    case CARRIAGE_RETURN = "\r";
    case SPACE           = "\040";
}
