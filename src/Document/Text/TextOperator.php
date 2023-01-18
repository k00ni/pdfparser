<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Text;

use PrinsFrank\PdfParser\Document\Text\OperatorString\TextPositioningOperator;
use PrinsFrank\PdfParser\Document\Text\OperatorString\TextShowingOperator;
use PrinsFrank\PdfParser\Document\Text\OperatorString\TextStateOperator;

class TextOperator
{
    public function __construct(
        public readonly TextPositioningOperator|TextShowingOperator|TextStateOperator $operator,
        public readonly string $operands
    ) {
    }
}
