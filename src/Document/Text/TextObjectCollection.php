<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Text;

use Override;
use PrinsFrank\PdfParser\Exception\RuntimeException;
use Stringable;

class TextObjectCollection implements Stringable {
    /** @var list<TextObject> */
    public readonly array $textObjects;

    /** @no-named-arguments */
    public function __construct(
        TextObject... $textObjects
    ) {
        $this->textObjects = $textObjects;
    }

    #[Override]
    public function __toString(): string {
        return preg_replace('/\h+/', ' ', trim(implode(' ', $this->textObjects)))
            ?? throw new RuntimeException();
    }
}
