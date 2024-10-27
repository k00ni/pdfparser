<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Unused\Text;

use PrinsFrank\PdfParser\Unused\Text\OperatorString\TextPositioningOperator;
use PrinsFrank\PdfParser\Unused\Text\OperatorString\TextShowingOperator;
use PrinsFrank\PdfParser\Unused\Text\OperatorString\TextStateOperator;
use Stringable;

class TextOperator implements Stringable {
    public function __construct(
        public readonly TextPositioningOperator|TextShowingOperator|TextStateOperator $operator,
        public readonly string $operands
    ) {
    }

    public function __toString(): string {
        if ($this->operator instanceof TextShowingOperator === false) {
            return '';
        }

        return match ($this->operator) {
            TextShowingOperator::SHOW,
            TextShowingOperator::MOVE_SHOW_SPACING,
            TextShowingOperator::MOVE_SHOW,
            TextShowingOperator::SHOW_ARRAY => $this->operands
        };
    }
}
