<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Text;

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
        foreach (str_split($text) as $char) {
            $operandBuffer->addChar($char);
            $operatorBuffer->next($char);
            if ($operatorBuffer->seenBackedEnumValue(TextObjectOperator::BEGIN)) {
                $operandBuffer->flush();
                $textObject = new TextObject();
                $textObjects[] = $textObject;
                continue;
            }

            if ($operatorBuffer->seenBackedEnumValue(TextObjectOperator::END)) {
                $operandBuffer->flush();
                $textObject = null;
                continue;
            }

            if ($textObject === null) {
                continue;
            }

            $operator = $operatorBuffer->getBackedEnumValue(TextPositioningOperator::class, TextShowingOperator::class, TextStateOperator::class, GraphicsStateOperator::class, ColorOperator::class);
            if ($operator instanceof TextPositioningOperator || $operator instanceof TextShowingOperator || $operator instanceof TextStateOperator || $operator instanceof GraphicsStateOperator || $operator instanceof ColorOperator) {
                $textObject->addTextOperator(new TextOperator($operator, trim($operandBuffer->removeChar(strlen($operator->value))->__toString())));
                $operandBuffer->flush();
            }
        }

        return new TextObjectCollection(...$textObjects);
    }
}
