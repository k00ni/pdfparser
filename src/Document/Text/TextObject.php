<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Text;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\ExtendedDictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Reference\ReferenceValue;
use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Document\Object\Decorator\Page;
use PrinsFrank\PdfParser\Document\Text\OperatorString\TextShowingOperator;
use PrinsFrank\PdfParser\Document\Text\OperatorString\TextStateOperator;

class TextObject {
    /** @var list<TextOperator> */
    public array $textOperators = [];

    public function addTextOperator(TextOperator $textOperator): self {
        $this->textOperators[] = $textOperator;

        return $this;
    }

    public function getText(Document $document, Page $page): string {
        $text = '';
        $font = null;
        foreach ($this->textOperators as $textOperator) {
            if ($textOperator->operator instanceof TextShowingOperator) {
                $text .= ' ' . $textOperator->operator->displayOperands($textOperator->operands, $font);
            } elseif ($textOperator->operator instanceof TextStateOperator) {
                $fontReference = $page->getFontDictionary($document)
                    ->getValueForKey(new ExtendedDictionaryKey('F' . $textOperator->operator->getFontNumber($textOperator->operands)), ReferenceValue::class);

                $font = $document->getObject($fontReference->objectNumber, TypeNameValue::FONT);
            }
        }

        return trim($text);
    }
}
