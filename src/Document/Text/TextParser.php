<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Text;

use PrinsFrank\PdfParser\Document\Generic\Operator\MarkedContentOperator;
use PrinsFrank\PdfParser\Document\Generic\Parsing\InfiniteBuffer;
use PrinsFrank\PdfParser\Document\Generic\Parsing\RollingCharBuffer;
use PrinsFrank\PdfParser\Document\Text\OperatorString\ColorOperator;
use PrinsFrank\PdfParser\Document\Text\OperatorString\GraphicsStateOperator;
use PrinsFrank\PdfParser\Document\Text\OperatorString\TextObjectOperator;
use PrinsFrank\PdfParser\Document\Text\OperatorString\TextPositioningOperator;
use PrinsFrank\PdfParser\Document\Text\OperatorString\TextShowingOperator;
use PrinsFrank\PdfParser\Document\Text\OperatorString\TextStateOperator;

class TextParser {
    public static function parse(string $text): TextObjectCollection {
        $operatorBuffer = new RollingCharBuffer(3);
        $textObject = null;
        $operandBuffer = new InfiniteBuffer();
        $textObjects = [];
        $inValue = false;
        file_put_contents('temp.txt', $text);
        $previousChar = null;
        foreach (str_split($text) as $char) {
            $operandBuffer->addChar($char);
            $operatorBuffer->next($char);
            if (in_array($char, ['[', '<', '('], true) && $previousChar !== '\\') {
                $inValue = true;
                $previousChar = $char;
                continue;
            }

            if ($inValue && in_array($char, [']', '>', ')'], true) && $previousChar !== '\\') {
                $inValue = false;
                $previousChar = $char;
                continue;
            }

            if ($inValue) {
                $previousChar = $char;
                continue;
            }

            if ($operatorBuffer->seenBackedEnumValue(TextObjectOperator::BEGIN)) {
                $operandBuffer->flush();
                $textObject = new TextObject();
                $textObjects[] = $textObject;
                $previousChar = $char;
                continue;
            }

            if ($operatorBuffer->seenBackedEnumValue(TextObjectOperator::END)) {
                $operandBuffer->flush();
                $textObject = null;
                $previousChar = $char;
                continue;
            }

            if ($operatorBuffer->seenBackedEnumValue(MarkedContentOperator::BeginMarkedContent)
                || $operatorBuffer->seenBackedEnumValue(MarkedContentOperator::BeginMarkedContentWithProperties)
                || $operatorBuffer->seenBackedEnumValue(MarkedContentOperator::EndMarkedContent)) {
                $operandBuffer->flush();
                $previousChar = $char;
                continue;
            }

            if ($textObject === null) {
                $previousChar = $char;
                continue;
            }

            $operator = $operatorBuffer->getBackedEnumValue(TextPositioningOperator::class, TextShowingOperator::class, TextStateOperator::class, GraphicsStateOperator::class, ColorOperator::class);
            if ($operator instanceof TextPositioningOperator || $operator instanceof TextShowingOperator || $operator instanceof TextStateOperator || $operator instanceof GraphicsStateOperator || $operator instanceof ColorOperator) {
                $textObject->addTextOperator(new TextOperator($operator, trim($operandBuffer->removeChar(strlen($operator->value))->__toString())));
                $operandBuffer->flush();
                $previousChar = $char;
            }
        }

        return new TextObjectCollection(...$textObjects);
    }
}
