<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Text\OperatorString;

enum TextObjectOperator: string {
    case BEGIN = 'BT';
    case END = 'ET';
}
