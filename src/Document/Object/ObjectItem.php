<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object;

use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParser;
use PrinsFrank\PdfParser\Document\Generic\Character\DelimiterCharacter;
use PrinsFrank\PdfParser\Exception\ParseFailureException;
use PrinsFrank\PdfParser\Stream;

class ObjectItem {
    private readonly ?Dictionary $dictionary;

    public function __construct(
        public readonly int $objectNumber,
        public readonly int $generationNumber,
        public readonly int $startOffset,
        public readonly int $endOffset,
    ) {
    }

    public function getDictionary(Stream $stream): ?Dictionary {
        if (isset($this->dictionary) === true) {
            return $this->dictionary;
        }

        $startDictionaryPos = $stream->strpos(DelimiterCharacter::LESS_THAN_SIGN->value . DelimiterCharacter::LESS_THAN_SIGN->value, $this->startOffset, $this->endOffset);
        if ($startDictionaryPos === null) {
            return $this->dictionary = null;
        }

        $endDictionaryPos = $stream->strrpos(DelimiterCharacter::GREATER_THAN_SIGN->value . DelimiterCharacter::GREATER_THAN_SIGN->value, $stream->getSizeInBytes() - $this->endOffset);
        if ($endDictionaryPos === null || $endDictionaryPos < $startDictionaryPos) {
            throw new ParseFailureException(sprintf('Couldn\'t find the end of the dictionary in "%s"', $stream->read($startDictionaryPos, $stream->getSizeInBytes() - $this->endOffset)));
        }

        return $this->dictionary = DictionaryParser::parse($stream, $startDictionaryPos, $endDictionaryPos - $startDictionaryPos + strlen(DelimiterCharacter::GREATER_THAN_SIGN->value . DelimiterCharacter::GREATER_THAN_SIGN->value));
    }
}
