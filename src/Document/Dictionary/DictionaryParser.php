<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary;

use Exception;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParseContext\DictionaryParseContext;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParseContext\NestingContext;
use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Document\Generic\Character\DelimiterCharacter;
use PrinsFrank\PdfParser\Document\Generic\Character\LiteralStringEscapeCharacter;
use PrinsFrank\PdfParser\Document\Generic\Character\WhitespaceCharacter;
use PrinsFrank\PdfParser\Document\Generic\Marker;
use PrinsFrank\PdfParser\Document\Generic\Parsing\RollingCharBuffer;
use PrinsFrank\PdfParser\Exception\BufferTooSmallException;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

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
     * @throws ParseFailureException
     */
    public static function parse(Document $document, string $content): Dictionary
    {
        $dictionaryArray = [];
        $rollingCharBuffer = new RollingCharBuffer(6);
        $nestingContext = (new NestingContext())->setContext(DictionaryParseContext::ROOT);
        foreach (str_split($content) as $char) {
            $rollingCharBuffer->next()->setCharacter($char);
            if ($rollingCharBuffer->seenMarker(Marker::STREAM)) {
                break;
            }

            if ($char === DelimiterCharacter::LESS_THAN_SIGN->value
                && $rollingCharBuffer->getPreviousCharacter() === DelimiterCharacter::LESS_THAN_SIGN->value
                && $rollingCharBuffer->getPreviousCharacter(2) !== LiteralStringEscapeCharacter::REVERSE_SOLIDUS->value) {
                if ($nestingContext->getContext() === DictionaryParseContext::KEY) {
                    $nestingContext->removeFromKeyBuffer();
                }

                $nestingContext->setContext(DictionaryParseContext::DICTIONARY)->incrementNesting()->setContext(DictionaryParseContext::DICTIONARY);
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
                } else if ($nestingContext->getContext() === DictionaryParseContext::COMMENT) {
                    $nestingContext->setContext(DictionaryParseContext::DICTIONARY);
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
            } else if ($char === DelimiterCharacter::PERCENT_SIGN->value && $rollingCharBuffer->getPreviousCharacter() !== LiteralStringEscapeCharacter::REVERSE_SOLIDUS->value) {
                $nestingContext->setContext(DictionaryParseContext::COMMENT);
            }

            match ($nestingContext->getContext()) {
                DictionaryParseContext::KEY => $nestingContext->addToKeyBuffer($char),
                DictionaryParseContext::VALUE_IN_PARENTHESES,
                DictionaryParseContext::VALUE_IN_SQUARE_BRACKETS,
                DictionaryParseContext::VALUE => $nestingContext->addToValueBuffer($char),
                default => null,
            };
        }

        return DictionaryFactory::fromArray($document, $dictionaryArray);
    }

    private static function flush(array &$dictionaryArray, NestingContext $nestingContext) : void
    {
        if ($nestingContext->getValueBuffer()->isEmpty() || $nestingContext->getKeyBuffer()->isEmpty()) {
            return;
        }

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
