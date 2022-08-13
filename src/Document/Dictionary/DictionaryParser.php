<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary;

use Exception;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParseContext\DictionaryParseContext;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParseContext\NestingContext;
use PrinsFrank\PdfParser\Document\Generic\Character\DelimiterCharacter;
use PrinsFrank\PdfParser\Document\Generic\Character\LiteralStringEscapeCharacter;
use PrinsFrank\PdfParser\Document\Generic\Character\WhitespaceCharacter;
use PrinsFrank\PdfParser\Document\Generic\Parsing\RollingCharBuffer;
use PrinsFrank\PdfParser\Exception\BufferTooSmallException;
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
     * @throws BufferTooSmallException
     */
    public static function parse(string $content): Dictionary
    {
        $dictionaryArray = [];
        $rollingCharBuffer = new RollingCharBuffer(3);
        $nestingContext = (new NestingContext())->setContext(DictionaryParseContext::ROOT);
        foreach (str_split($content) as $char) {
            $rollingCharBuffer->next()->setCharacter($char);
            if ($char === DelimiterCharacter::LESS_THAN_SIGN->value
                && $rollingCharBuffer->getPreviousCharacter() === DelimiterCharacter::LESS_THAN_SIGN->value
                && $rollingCharBuffer->getPreviousCharacter(2) !== LiteralStringEscapeCharacter::REVERSE_SOLIDUS->value) {
                $nestingContext->removeFromKeyBuffer()
                               ->setContext(DictionaryParseContext::DICTIONARY)
                               ->incrementNesting()
                               ->setContext(DictionaryParseContext::DICTIONARY);
            } else if ($char === DelimiterCharacter::GREATER_THAN_SIGN->value
                && $rollingCharBuffer->getPreviousCharacter() === DelimiterCharacter::GREATER_THAN_SIGN->value
                && $rollingCharBuffer->getPreviousCharacter(2) !== LiteralStringEscapeCharacter::REVERSE_SOLIDUS->value) {
                $nestingContext->removeFromValueBuffer();
                self::flush($dictionaryArray, $nestingContext);
                $nestingContext->decrementNesting()->flush();
            } else if ($char === DelimiterCharacter::SOLIDUS->value && $rollingCharBuffer->getPreviousCharacter() !== LiteralStringEscapeCharacter::REVERSE_SOLIDUS->value) {
                if ($nestingContext->getContext() === DictionaryParseContext::DICTIONARY) {
                    $nestingContext->setContext(DictionaryParseContext::KEY);
                } else if ($nestingContext->getContext() === DictionaryParseContext::VALUE) {
                    self::flush($dictionaryArray, $nestingContext);
                    $nestingContext->setContext(DictionaryParseContext::KEY);
                } else if ($nestingContext->getContext() === DictionaryParseContext::KEY || $nestingContext->getContext() === DictionaryParseContext::KEY_VALUE_SEPARATOR) {
                    $nestingContext->setContext(DictionaryParseContext::VALUE);
                }
            } else if ($char === LiteralStringEscapeCharacter::LINE_FEED->value) {
                if ($nestingContext->getContext() === DictionaryParseContext::KEY) {
                    $nestingContext->setContext(DictionaryParseContext::VALUE);
                } else if ($nestingContext->getContext() === DictionaryParseContext::VALUE) {
                    self::flush($dictionaryArray, $nestingContext);
                }
            } else if ($char === WhitespaceCharacter::SPACE->value && $nestingContext->getContext() === DictionaryParseContext::KEY) {
                $nestingContext->setContext(DictionaryParseContext::KEY_VALUE_SEPARATOR);
            } else if ($char === DelimiterCharacter::LEFT_PARENTHESIS->value
                       && (in_array($nestingContext->getContext(), [DictionaryParseContext::KEY, DictionaryParseContext::KEY_VALUE_SEPARATOR, DictionaryParseContext::VALUE], true))) {
                $nestingContext->setContext(DictionaryParseContext::VALUE_IN_PARENTHESES);
            } else if ($char === DelimiterCharacter::RIGHT_PARENTHESIS->value && $nestingContext->getContext() === DictionaryParseContext::VALUE_IN_PARENTHESES) {
                $nestingContext->setContext(DictionaryParseContext::VALUE);
            } else if ($char === DelimiterCharacter::LEFT_SQUARE_BRACKET->value
                       && (in_array($nestingContext->getContext(), [DictionaryParseContext::KEY, DictionaryParseContext::KEY_VALUE_SEPARATOR, DictionaryParseContext::VALUE], true))) {
                $nestingContext->setContext(DictionaryParseContext::VALUE_IN_SQUARE_BRACKETS);
            } else if ($char === DelimiterCharacter::RIGHT_SQUARE_BRACKET->value && $nestingContext->getContext() === DictionaryParseContext::VALUE_IN_SQUARE_BRACKETS) {
                $nestingContext->setContext(DictionaryParseContext::VALUE);
            } else if (trim($char) !== '' && $nestingContext->getContext() === DictionaryParseContext::KEY_VALUE_SEPARATOR) {
                $nestingContext->setContext(DictionaryParseContext::VALUE);
            }

            match ($nestingContext->getContext()) {
                DictionaryParseContext::KEY => $nestingContext->addToKeyBuffer($char),
                DictionaryParseContext::VALUE_IN_PARENTHESES,
                DictionaryParseContext::VALUE_IN_SQUARE_BRACKETS,
                DictionaryParseContext::VALUE => $nestingContext->addToValueBuffer($char),
                default => null,
            };

            echo 'Char "'. $char . '" resulted in new context "'. $nestingContext->getContext()->name . '"' . PHP_EOL;
        }

        var_dump($dictionaryArray);
        try {
            $dictionary = DictionaryFactory::fromArray($dictionaryArray);
        } catch (Throwable $e) {
            throw new Exception('Unable to create dictionary for string "' . $content . '": ' . $e->getMessage());
        }

        return $dictionary;
    }

    private static function flush(array &$dictionaryArray, NestingContext $nestingContext) : void
    {
        $dictionaryArrayPointer = &$dictionaryArray;
        foreach ($nestingContext->getKeysFromRoot() as $key) {
            if ($key === (string) $nestingContext->getKeyBuffer()) {
                break;
            }

            $dictionaryArrayPointer = &$dictionaryArrayPointer[trim($key)];
        }

        $dictionaryArrayPointer[(string) $nestingContext->getKeyBuffer()] = trim((string) $nestingContext->getValueBuffer());
        $nestingContext->flush()->setContext(DictionaryParseContext::ROOT);
    }
}
