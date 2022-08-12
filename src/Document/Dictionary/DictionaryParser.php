<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary;

use Exception;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParseContext\DictionaryParseContext;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParseContext\NestingContext;
use PrinsFrank\PdfParser\Document\Generic\Character\DelimiterCharacter;
use PrinsFrank\PdfParser\Document\Generic\Character\LiteralStringEscapeCharacter;
use PrinsFrank\PdfParser\Document\Generic\Character\WhitespaceCharacter;
use PrinsFrank\PdfParser\Document\Generic\Parsing\InfiniteBuffer;
use PrinsFrank\PdfParser\Document\Generic\Parsing\RollingCharBuffer;
use PrinsFrank\PdfParser\Exception\ParseFailureException;
use Throwable;

/**
 * << start object
 * >> end object
 * [ start array
 * ] end array
 * / start key
 *
 */
class DictionaryParser
{
    /**
     * @throws ParseFailureException
     */
    public static function parse(string $content): Dictionary
    {
        $previousContext = DictionaryParseContext::ROOT;

        $dictionaryArray = [];
        $rollingCharBuffer = new RollingCharBuffer(3);
        $nestingContext = new NestingContext();
        $keyBuffer = clone $valueBuffer = new InfiniteBuffer();
        foreach (str_split($content) as $char) {
            $rollingCharBuffer->next()->setCharacter($char);
            if ($nestingContext->getContext() === DictionaryParseContext::KEY_VALUE_SEPARATOR) {
                $nestingContext->setContext(DictionaryParseContext::VALUE);
            } else if ($char === DelimiterCharacter::LESS_THAN_SIGN->value
                && $rollingCharBuffer->getPreviousCharacter() === DelimiterCharacter::LESS_THAN_SIGN->value
                && $rollingCharBuffer->getPreviousCharacter(2) !== LiteralStringEscapeCharacter::REVERSE_SOLIDUS->value) {
                $nestingContext->incrementNesting()->setContext(DictionaryParseContext::ROOT);
            } else if ($char === DelimiterCharacter::GREATER_THAN_SIGN->value
                && $rollingCharBuffer->getPreviousCharacter() === DelimiterCharacter::GREATER_THAN_SIGN->value
                && $rollingCharBuffer->getPreviousCharacter(2) !== LiteralStringEscapeCharacter::REVERSE_SOLIDUS->value) {
                self::flush($dictionaryArray, $keyBuffer, $valueBuffer->setValue(substr($valueBuffer->__toString(), 0, -1)), $nestingContext);
                $nestingContext->decrementNesting()->setContext(DictionaryParseContext::ROOT);
            } else if ($char === DelimiterCharacter::SOLIDUS->value && $rollingCharBuffer->getPreviousCharacter() !== LiteralStringEscapeCharacter::REVERSE_SOLIDUS->value) {
                if ($nestingContext->getContext() === DictionaryParseContext::ROOT) {
                    $nestingContext->setContext(DictionaryParseContext::KEY);
                } else if ($nestingContext->getContext() === DictionaryParseContext::VALUE) {
                    self::flush($dictionaryArray, $keyBuffer, $valueBuffer, $nestingContext);
                    $nestingContext->setContext(DictionaryParseContext::KEY);
                } else if ($nestingContext->getContext() === DictionaryParseContext::KEY) {
                    $nestingContext->setContext(DictionaryParseContext::VALUE);
                }
            } else if ($char === LiteralStringEscapeCharacter::LINE_FEED->value) {
                if ($nestingContext->getContext() === DictionaryParseContext::KEY) {
                    $nestingContext->setContext(DictionaryParseContext::VALUE);
                } else if ($nestingContext->getContext() === DictionaryParseContext::VALUE) {
                    self::flush($dictionaryArray, $keyBuffer, $valueBuffer, $nestingContext);
                }
            } else if ($char === WhitespaceCharacter::SPACE->value && $nestingContext->getContext() === DictionaryParseContext::KEY) {
                $nestingContext->setContext(DictionaryParseContext::KEY_VALUE_SEPARATOR);
            }

            match ($nestingContext->getContext()) {
                DictionaryParseContext::KEY => $keyBuffer->addChar($char),
                DictionaryParseContext::VALUE => $valueBuffer->addChar($char),
                default => null,
            };

            echo 'Char: "' . $char . '", previous context: "' . $previousContext->name . '", new context: "' . $nestingContext->getContext()->name . '"' . PHP_EOL;
            $previousContext = $nestingContext->getContext();
        }

        try {
            $dictionary = DictionaryFactory::fromArray($dictionaryArray);
        } catch (Throwable $e) {
            throw new Exception('Unable to create dictionary for string "' . $content . '": ' . $e->getMessage());
        }

        return $dictionary;
    }

    private static function flush(array &$dictionaryArray, InfiniteBuffer $keyBuffer, InfiniteBuffer $valueBuffer, NestingContext $nestingContext) : void
    {
        $dictionaryArray[trim((string) $keyBuffer)] = trim((string) $valueBuffer);
        $keyBuffer->flush();
        $valueBuffer->flush();
        $nestingContext->setContext(DictionaryParseContext::ROOT);
    }
}
