<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Text;

use Stringable;

class TextObjectCollection implements Stringable {
    /** @var array<TextObject> */
    public array $textObjects;

    public function __construct(TextObject... $textObjects) {
        $this->textObjects = $textObjects;
    }

    public function addTextObject(TextObject $textObject): self {
        $this->textObjects[] = $textObject;

        return $this;
    }

    public function __toString(): string {
        return implode('', $this->textObjects);
    }
}
