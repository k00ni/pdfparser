<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Text;

use Stringable;

class TextObject implements Stringable {
    /** @var list<TextOperator> */
    public array $textOperators = [];

    public function addTextOperator(TextOperator $textOperator): self {
        $this->textOperators[] = $textOperator;

        return $this;
    }

    public function __toString(): string {
        return implode('', $this->textOperators);
    }
}
