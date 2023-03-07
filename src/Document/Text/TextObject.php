<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Text;

class TextObject
{
    /** @var array<TextOperator> */
    public array $textOperators;

    public function __construct(TextOperator... $textOperators)
    {
        $this->textOperators = $textOperators;
    }

    public function addTextOperator(TextOperator $textOperator): self
    {
        $this->textOperators[] = $textOperator;

        return $this;
    }
}
