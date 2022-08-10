<?php
declare(strict_types=1);

enum WhitespaceCharacter: string
{
    case NULL            = "\000";
    case HORIZONTAL_TAB  = "\t";
    case LINE_FEED       = "\n";
    case FORM_FEED       = "\f";
    case CARRIAGE_RETURN = "\r";
    case SPACE           = "\040";
}
