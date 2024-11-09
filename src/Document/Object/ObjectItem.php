<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object;

use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParser;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Generic\Character\DelimiterCharacter;
use PrinsFrank\PdfParser\Document\Object\ObjectStream\ObjectStreamData;
use PrinsFrank\PdfParser\Document\Object\ObjectStream\ObjectStreamDataParser;
use PrinsFrank\PdfParser\Exception\ParseFailureException;
use PrinsFrank\PdfParser\Stream;

class ObjectItem {
    private readonly ?Dictionary $dictionary;
    private readonly ?ObjectStreamData $objectStreamData;

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

        $startDictionaryPos = $stream->firstPos(DelimiterCharacter::LESS_THAN_SIGN, $this->startOffset, $this->endOffset);
        if ($startDictionaryPos === null) {
            return $this->dictionary = null;
        }

        $endDictionaryPos = $stream->lastPos(DelimiterCharacter::GREATER_THAN_SIGN, $stream->getSizeInBytes() - $this->endOffset);
        if ($endDictionaryPos === null || $endDictionaryPos < $startDictionaryPos) {
            throw new ParseFailureException(sprintf('Couldn\'t find the end of the dictionary in "%s"', $stream->read($startDictionaryPos, $stream->getSizeInBytes() - $this->endOffset)));
        }

        return $this->dictionary = DictionaryParser::parse($stream, $startDictionaryPos, $endDictionaryPos - $startDictionaryPos + strlen(DelimiterCharacter::GREATER_THAN_SIGN->value . DelimiterCharacter::GREATER_THAN_SIGN->value));
    }

    public function getStreamData(Stream $stream): ?ObjectStreamData {
        if (isset($this->objectStreamData)) {
            return $this->objectStreamData;
        }

        $dictionary = $this->getDictionary($stream);
        if ($dictionary->getValueForKey(DictionaryKey::TYPE) !== TypeNameValue::OBJ_STM) {
            throw new ParseFailureException('Unable to get stream data from item that is not a stream');
        }

        return $this->objectStreamData = ObjectStreamDataParser::parse(
            $stream,
            $this->startOffset,
            $this->endOffset,
            $dictionary
        );
    }
}
