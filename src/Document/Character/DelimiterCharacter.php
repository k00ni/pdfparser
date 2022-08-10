<?php
declare(strict_types=1);

enum DelimiterCharacter: string
{
    case LEFT_PARENTHESIS     = '(';
    case RIGHT_PARENTHESIS    = ')';
    case LESS_THAN_SIGN       = '<';
    case GREATER_THAN_SIGN    = '>';
    case LEFT_SQUARE_BRACKET  = '[';
    case RIGHT_SQUARE_BRACKET = ']';
    case LEFT_CURLY_BRACKET   = '{';
    case RIGHT_CURLY_BRACKET  = '}';
    case SOLIDUS              = '/';
    case PERCENT_SIGN         = '%';
}
