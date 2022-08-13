<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryParseContext;

enum DictionaryParseContext
{
    case ROOT;
    case DICTIONARY;
    case KEY;
    case KEY_VALUE_SEPARATOR;
    case VALUE;
    case VALUE_IN_PARENTHESES;
    case VALUE_IN_SQUARE_BRACKETS;
}
