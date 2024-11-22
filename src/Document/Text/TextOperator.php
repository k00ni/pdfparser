<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Text;

use Override;
use PrinsFrank\PdfParser\Document\Text\OperatorString\TextPositioningOperator;
use PrinsFrank\PdfParser\Document\Text\OperatorString\TextShowingOperator;
use PrinsFrank\PdfParser\Document\Text\OperatorString\TextStateOperator;
use PrinsFrank\PdfParser\Exception\RuntimeException;
use Stringable;

class TextOperator implements Stringable {
    public function __construct(
        public readonly TextPositioningOperator|TextShowingOperator|TextStateOperator $operator,
        public readonly string $operands
    ) {
    }

    #[Override]
    public function __toString(): string {
        if ($this->operator instanceof TextShowingOperator === false) {
            return '';
        }

        if ($this->operator === TextShowingOperator::SHOW_ARRAY) {
            return preg_replace('/\(([^)]+)\)(-?[0-9]+(.[0-9]+)?)?/', '$1', rtrim(ltrim($this->operands, '['), ']'))
                ?? throw new RuntimeException();
        }

        if ($this->operator === TextShowingOperator::SHOW) {
            return rtrim(ltrim($this->operands, '('), ')');
        }

        return PHP_EOL . $this->operands;
    }
}
