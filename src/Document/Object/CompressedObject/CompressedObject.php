<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\CompressedObject;

use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParser;
use PrinsFrank\PdfParser\Stream;

class CompressedObject {
    public function __construct(
        private readonly Stream $streamContent,
        private readonly int $byteOffsetStart,
        private readonly int $byteOffsetEnd,
    ) {
    }

    public function getDictionary(): Dictionary {
        return DictionaryParser::parse(
            $this->streamContent,
            $this->byteOffsetStart,
            $this->byteOffsetEnd - $this->byteOffsetStart
        );
    }
}
