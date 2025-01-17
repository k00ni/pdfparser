<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Text;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\ExtendedDictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Reference\ReferenceValue;
use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Document\Object\Decorator\Font;
use PrinsFrank\PdfParser\Document\Object\Decorator\Page;
use PrinsFrank\PdfParser\Document\Text\OperatorString\TextPositioningOperator;
use PrinsFrank\PdfParser\Document\Text\OperatorString\TextShowingOperator;
use PrinsFrank\PdfParser\Document\Text\OperatorString\TextStateOperator;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

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
            if ($textOperator->operator instanceof TextPositioningOperator) {
                $text .= $textOperator->operator->display($textOperator->operands);
            } elseif ($textOperator->operator instanceof TextShowingOperator) {
                $text .= $textOperator->operator->displayOperands($textOperator->operands, $font);
            } elseif ($textOperator->operator === TextStateOperator::FONT_SIZE) {
                $fontReference = $page->getFontDictionary()
                    ?->getValueForKey(new ExtendedDictionaryKey('F' . $textOperator->operator->getFontNumber($textOperator->operands)), ReferenceValue::class)
                    ?? throw new ParseFailureException(sprintf('Unable to locate font with number %s', $textOperator->operator->getFontNumber($textOperator->operands)));

                $font = $document->getObject($fontReference->objectNumber, Font::class);
            }
        }

        return trim($text);
    }
}
