<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryParseContext;

enum DictionaryParseContext
{
    case ROOT;
    case KEY;
    case KEY_VALUE_SEPARATOR;
    case VALUE;
    case EXPLICIT_VALUE;
}
