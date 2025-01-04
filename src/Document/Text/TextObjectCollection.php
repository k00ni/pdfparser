<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Text;

use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Document\Object\Decorator\Page;

class TextObjectCollection {
    /** @var list<TextObject> */
    public readonly array $textObjects;

    /** @no-named-arguments */
    public function __construct(
        TextObject... $textObjects
    ) {
        $this->textObjects = $textObjects;
    }

    public function getText(Document $document, Page $page): string {
        $text = '';
        foreach ($this->textObjects as $textObject) {
            if (($textObjectText = trim($textObject->getText($document, $page))) === '') {
                continue;
            }

            $text .= ' ' . $textObjectText;
        }

        return preg_replace('/\h+([.,!?])/', '$1', str_replace('  ', ' ', trim($text)));
    }
}
